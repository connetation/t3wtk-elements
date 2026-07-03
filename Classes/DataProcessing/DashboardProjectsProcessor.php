<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

final class DashboardProjectsProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $targetVar = $processorConfiguration['as'] ?? 'projects';
        $contentElementUid = (int)($processedData['data']['uid'] ?? 0);

        if ($contentElementUid === 0) {
            $processedData[$targetVar] = [];
            $processedData['categories'] = [];
            $processedData['settings'] = $this->defaultSettings();
            return $processedData;
        }

        // ─── FlexForm settings ────────────────────────────────────
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $flexData = $flexFormService->convertFlexFormContentToArray(
            $processedData['data']['tx_conn_elements_dashboard_settings'] ?? ''
        );
        $processedData['settings'] = $this->resolveSettings($flexData['settings'] ?? []);

        // ─── Projects ─────────────────────────────────────────────
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_dashboard_project');

        $rows = $queryBuilder
            ->select('*')
            ->from('tx_conn_elements_domain_model_dashboard_project')
            ->where(
                $queryBuilder->expr()->eq('content_element', $queryBuilder->createNamedParameter($contentElementUid, ParameterType::INTEGER)),
                $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, ParameterType::INTEGER)),
                $queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, ParameterType::INTEGER))
            )
            ->orderBy('sorting', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        // ─── Categories ───────────────────────────────────────────
        $catQb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_dashboard_projectcategory');

        $catRows = $catQb
            ->select('uid', 'title')
            ->from('tx_conn_elements_domain_model_dashboard_projectcategory')
            ->where(
                $catQb->expr()->eq('content_element', $catQb->createNamedParameter($contentElementUid, ParameterType::INTEGER)),
                $catQb->expr()->eq('deleted', $catQb->createNamedParameter(0, ParameterType::INTEGER)),
                $catQb->expr()->eq('hidden', $catQb->createNamedParameter(0, ParameterType::INTEGER))
            )
            ->orderBy('title', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $categoryMap = [];
        $categories  = [];
        foreach ($catRows as $catRow) {
            $categoryMap[(int)$catRow['uid']] = $catRow['title'];
            $categories[] = ['uid' => (int)$catRow['uid'], 'title' => $catRow['title']];
        }

        $siteLanguage    = $cObj->getRequest()->getAttribute('language');
        $languageService = GeneralUtility::makeInstance(LanguageServiceFactory::class)
            ->createFromSiteLanguage($siteLanguage);
        $fileRepository  = GeneralUtility::makeInstance(FileRepository::class);

        $allTagsSet = [];
        $processedData[$targetVar] = array_map(
            static function (array $row) use ($languageService, $fileRepository, $categoryMap, &$allTagsSet): array {
                $row['tagsArray'] = $row['tags'] !== ''
                    ? array_map('trim', explode(',', $row['tags']))
                    : [];
                $row['tagsList'] = implode(',', array_map('mb_strtolower', $row['tagsArray']));
                foreach ($row['tagsArray'] as $t) {
                    $allTagsSet[mb_strtolower($t)] = $t;
                }
                $row['statusLabel']  = $languageService->sL(
                    'LLL:EXT:conn_elements/Resources/Private/Language/locallang.xlf:dashboard.status.' . $row['status']
                ) ?: ucwords(str_replace('_', ' ', $row['status']));
                $row['categoryTitle'] = $categoryMap[(int)$row['category']] ?? '';
                $row['imageFiles']   = (int)$row['image'] > 0
                    ? $fileRepository->findByRelation('tx_conn_elements_domain_model_dashboard_project', 'image', $row['uid'])
                    : [];
                return $row;
            },
            $rows
        );

        $processedData['categories'] = $categories;

        $allTagsSorted = array_values($allTagsSet);
        sort($allTagsSorted);
        $processedData['tags'] = $allTagsSorted;

        return $processedData;
    }

    private function resolveSettings(array $settings): array
    {
        $defaultView = in_array($settings['defaultView'] ?? '', ['grid', 'list', 'compact'], true)
            ? $settings['defaultView']
            : 'grid';

        $columns = max(2, min(4, (int)($settings['columns'] ?? 3)));

        $colClass = match ($columns) {
            2       => 'col-sm-6',
            4       => 'col-sm-6 col-lg-3',
            default => 'col-sm-6 col-lg-4',
        };

        $compactColumns = max(3, min(6, (int)($settings['compactColumns'] ?? 4)));

        $statsStyle = in_array($settings['statsStyle'] ?? 'default', ['default', 'compact'], true)
            ? ($settings['statsStyle'] ?? 'default')
            : 'default';

        $filterStyle = in_array($settings['filterStyle'] ?? 'default', ['default', 'compact'], true)
            ? ($settings['filterStyle'] ?? 'default')
            : 'default';

        $tagsStyle = in_array($settings['tagsStyle'] ?? 'row', ['row', 'dropdown', 'hidden'], true)
            ? ($settings['tagsStyle'] ?? 'row')
            : 'row';

        $cardVariant = in_array($settings['cardVariant'] ?? 'top', ['top', 'horizontal', 'logo', 'text'], true)
            ? ($settings['cardVariant'] ?? 'top')
            : 'top';

        return [
            'defaultView'    => $defaultView,
            'columns'        => $columns,
            'colClass'       => $colClass,
            'compactColumns' => $compactColumns,
            'cardVariant'    => $cardVariant,
            'showStats'      => (bool)($settings['showStats'] ?? true),
            'statsStyle'     => $statsStyle,
            'showFilter'     => (bool)($settings['showFilter'] ?? true),
            'filterStyle'    => $filterStyle,
            'tagsStyle'      => $tagsStyle,
            'showSearch'     => (bool)($settings['showSearch'] ?? true),
            'showModal'      => (bool)($settings['showModal'] ?? true),
        ];
    }

    private function defaultSettings(): array
    {
        return [
            'defaultView'    => 'grid',
            'columns'        => 3,
            'colClass'       => 'col-sm-6 col-lg-4',
            'compactColumns' => 4,
            'cardVariant'    => 'top',
            'showStats'      => true,
            'statsStyle'     => 'default',
            'showFilter'     => true,
            'filterStyle'    => 'default',
            'tagsStyle'      => 'row',
            'showSearch'     => true,
            'showModal'      => true,
        ];
    }
}
