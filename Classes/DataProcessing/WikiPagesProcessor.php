<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

final class WikiPagesProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $targetVar = $processorConfiguration['as'] ?? 'wikiPages';
        $wikiId    = (int)($processedData['data']['tx_conn_elements_wiki_id'] ?? 0);

        $empty = fn () => array_merge($processedData, [
            $targetVar    => [],
            'wiki'        => null,
            'categories'  => [],
            'tags'        => [],
            'settings'    => $this->defaultSettings(),
        ]);

        if ($wikiId === 0) {
            return $empty();
        }

        // ─── FlexForm settings ────────────────────────────────────
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $flexData        = $flexFormService->convertFlexFormContentToArray(
            $processedData['data']['tx_conn_elements_wiki_settings'] ?? ''
        );
        $processedData['settings'] = $this->resolveSettings($flexData['settings'] ?? []);

        // ─── Wiki directory record ────────────────────────────────
        $wikiQb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_wiki');
        $wiki = $wikiQb->select('*')
            ->from('tx_conn_elements_domain_model_wiki')
            ->where($wikiQb->expr()->eq('uid', $wikiQb->createNamedParameter($wikiId, ParameterType::INTEGER)))
            ->executeQuery()
            ->fetchAssociative();

        if (!$wiki) {
            return $empty();
        }
        $processedData['wiki'] = $wiki;

        // ─── Pages ────────────────────────────────────────────────
        $pageQb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_wikipage');
        $pages = $pageQb->select('*')
            ->from('tx_conn_elements_domain_model_wikipage')
            ->where(
                $pageQb->expr()->eq('wiki', $pageQb->createNamedParameter($wikiId, ParameterType::INTEGER)),
                $pageQb->expr()->eq('deleted', $pageQb->createNamedParameter(0, ParameterType::INTEGER)),
                $pageQb->expr()->eq('hidden', $pageQb->createNamedParameter(0, ParameterType::INTEGER))
            )
            ->orderBy('sorting', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        if (empty($pages)) {
            $processedData[$targetVar] = [];
            $processedData['categories'] = [];
            $processedData['tags'] = [];
            return $processedData;
        }

        $pageUids = array_column($pages, 'uid');

        // ─── Blocks per page ─────────────────────────────────────
        $blockQb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_wikipage_block');
        $blockRows = $blockQb->select('*')
            ->from('tx_conn_elements_domain_model_wikipage_block')
            ->where(
                $blockQb->expr()->in('page', $blockQb->createNamedParameter($pageUids, \Doctrine\DBAL\ArrayParameterType::INTEGER)),
                $blockQb->expr()->eq('deleted', $blockQb->createNamedParameter(0, ParameterType::INTEGER)),
                $blockQb->expr()->eq('hidden', $blockQb->createNamedParameter(0, ParameterType::INTEGER))
            )
            ->orderBy('sorting', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $blocksByPage = [];
        foreach ($blockRows as $block) {
            if ($block['type'] === 'code') {
                $content = $block['content'] ?? '';
                if ($content === '' && ($block['code_content'] ?? '') !== '') {
                    // Migrate from old separate code_content field.
                    $raw = $block['code_content'];
                    $content = str_contains($raw, '<')
                        ? html_entity_decode(strip_tags($raw), ENT_QUOTES | ENT_HTML5)
                        : $raw;
                } elseif (str_contains($content, '<p') || str_contains($content, '<h')) {
                    // Convert paragraph/line breaks to newlines before stripping tags so
                    // multi-line code blocks keep their structure.
                    $content = preg_replace('#</p>\s*<p[^>]*>#i', "\n", $content);
                    $content = preg_replace('#<br\s*/?>#i', "\n", $content);
                    $content = trim(html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5));
                }
                $block['content'] = $content;
            }
            $blocksByPage[(int)$block['page']][] = $block;
        }

        // ─── Categories ───────────────────────────────────────────
        $catQb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_wikicategory');
        $catRows = $catQb->select('uid', 'title')
            ->from('tx_conn_elements_domain_model_wikicategory')
            ->where(
                $catQb->expr()->eq('wiki', $catQb->createNamedParameter($wikiId, ParameterType::INTEGER)),
                $catQb->expr()->eq('deleted', $catQb->createNamedParameter(0, ParameterType::INTEGER)),
                $catQb->expr()->eq('hidden', $catQb->createNamedParameter(0, ParameterType::INTEGER))
            )
            ->orderBy('title', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $categoryMap = array_column($catRows, 'title', 'uid');
        $processedData['categories'] = $catRows;

        // ─── Assemble pages + collect all tags ───────────────────
        $allTagsSet = [];
        $processedData[$targetVar] = array_map(
            static function (array $row) use ($blocksByPage, $categoryMap, &$allTagsSet): array {
                $row['blocks']        = $blocksByPage[(int)$row['uid']] ?? [];
                $row['categoryTitle'] = $categoryMap[(int)$row['category']] ?? '';

                $pageTags = $row['tags'] !== ''
                    ? array_filter(array_map('trim', explode(',', $row['tags'])))
                    : [];
                $row['tagsArray'] = $pageTags;
                $row['tagsList']  = implode(',', array_map('mb_strtolower', $pageTags));

                foreach ($pageTags as $t) {
                    $allTagsSet[mb_strtolower($t)] = $t;
                }

                return $row;
            },
            $pages
        );

        $allTagsSorted = array_values($allTagsSet);
        sort($allTagsSorted);
        $processedData['tags'] = $allTagsSorted;

        return $processedData;
    }

    private function resolveSettings(array $settings): array
    {
        return [
            'show_search'          => (bool)($settings['show_search'] ?? true),
            'show_category_filter' => (bool)($settings['show_category_filter'] ?? true),
            'show_tag_filter'      => (bool)($settings['show_tag_filter'] ?? true),
        ];
    }

    private function defaultSettings(): array
    {
        return [
            'show_search'          => true,
            'show_category_filter' => true,
            'show_tag_filter'      => true,
        ];
    }
}
