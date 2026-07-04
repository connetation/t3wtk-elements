<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

final class WikiPagesProcessor extends AbstractItemsProcessor
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $targetVar = $processorConfiguration['as'] ?? $this->getDefaultTargetVariable();
        $wikiId    = (int)($processedData['data']['tx_conn_elements_wiki_id'] ?? 0);

        $empty = fn () => array_merge($processedData, [
            $targetVar    => [],
            'wiki'        => null,
            'categories'  => [],
            'tags'        => [],
            'settings'    => array_replace_recursive(
                $this->getTypoScriptSettings($contentObjectConfiguration),
                $this->defaultSettings('')
            ),
        ]);

        if ($wikiId === 0) {
            return $empty();
        }

        // ─── FlexForm settings (merged over the TypoScript settings) ─
        $processedData['settings'] = array_replace_recursive(
            $this->getTypoScriptSettings($contentObjectConfiguration),
            $this->resolveSettings(
                $this->decodeFlexFormSettings($processedData['data'], $this->getSettingsField($processorConfiguration)),
                ''
            )
        );

        // ─── Wiki directory record ────────────────────────────────
        $wikiQb = $this->createFrontendQueryBuilder('tx_conn_elements_domain_model_wiki');
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
        $pages = $this->fetchChildren($this->getTable(), $wikiId, 'wiki');

        if (empty($pages)) {
            $processedData[$targetVar] = [];
            $processedData['categories'] = [];
            $processedData['tags'] = [];
            return $processedData;
        }

        $pageUids = array_column($pages, 'uid');

        // ─── Blocks per page ─────────────────────────────────────
        $blockQb = $this->createFrontendQueryBuilder('tx_conn_elements_domain_model_wikipage_block');
        $blockRows = $blockQb->select('*')
            ->from('tx_conn_elements_domain_model_wikipage_block')
            ->where(
                $blockQb->expr()->in('page', $blockQb->createNamedParameter($pageUids, ArrayParameterType::INTEGER))
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
        $catQb = $this->createFrontendQueryBuilder('tx_conn_elements_domain_model_wikicategory');
        $catRows = $catQb->select('uid', 'title')
            ->from('tx_conn_elements_domain_model_wikicategory')
            ->where(
                $catQb->expr()->eq('wiki', $catQb->createNamedParameter($wikiId, ParameterType::INTEGER))
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

    protected function getTable(): string
    {
        return 'tx_conn_elements_domain_model_wikipage';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'wikiPages';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_elements_wiki_settings';
    }

    protected function resolveSettings(array $settings, string $settingsField): array
    {
        return [
            'show_search'          => (bool)($settings['show_search'] ?? true),
            'show_category_filter' => (bool)($settings['show_category_filter'] ?? true),
            'show_tag_filter'      => (bool)($settings['show_tag_filter'] ?? true),
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'show_search'          => true,
            'show_category_filter' => true,
            'show_tag_filter'      => true,
        ];
    }
}
