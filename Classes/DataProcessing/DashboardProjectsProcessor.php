<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

final class DashboardProjectsProcessor extends AbstractItemsProcessor
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $targetVar = $processorConfiguration['as'] ?? $this->getDefaultTargetVariable();
        $contentElementUid = (int)($processedData['data']['uid'] ?? 0);

        if ($contentElementUid === 0) {
            $processedData[$targetVar] = [];
            $processedData['categories'] = [];
            $processedData['settings'] = array_replace_recursive(
                $this->getTypoScriptSettings($contentObjectConfiguration),
                $this->defaultSettings('')
            );
            return $processedData;
        }

        // ─── FlexForm settings (merged over the TypoScript settings) ─
        $processedData['settings'] = array_replace_recursive(
            $this->getTypoScriptSettings($contentObjectConfiguration),
            $this->resolveSettings(
                $this->decodeFlexFormSettings($processedData['data'], $this->getSettingsField($processorConfiguration)),
                ''
            )
        );

        // ─── Projects ─────────────────────────────────────────────
        $rows = $this->fetchChildren($this->getTable(), $contentElementUid);

        // ─── Categories ───────────────────────────────────────────
        $catQb = $this->createFrontendQueryBuilder('tx_conn_elements_domain_model_dashboard_projectcategory');
        $catRows = $catQb
            ->select('uid', 'title')
            ->from('tx_conn_elements_domain_model_dashboard_projectcategory')
            ->where(
                $catQb->expr()->eq('content_element', $catQb->createNamedParameter($contentElementUid, \Doctrine\DBAL\ParameterType::INTEGER))
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

        // ─── Resolve FAL images (batched, one query) ──────────────
        $imagesByProject = $this->resolveFileReferences(
            $this->getTable(),
            'image',
            array_column($rows, 'uid')
        );

        $allTagsSet = [];
        $processedData[$targetVar] = array_map(
            static function (array $row) use ($languageService, $imagesByProject, $categoryMap, &$allTagsSet): array {
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
                $row['imageFiles']   = $imagesByProject[(int)$row['uid']] ?? [];
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

    protected function getTable(): string
    {
        return 'tx_conn_elements_domain_model_dashboard_project';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'projects';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_elements_dashboard_settings';
    }

    protected function resolveSettings(array $settings, string $settingsField): array
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

    protected function defaultSettings(string $settingsField): array
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
