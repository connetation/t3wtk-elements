<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

final class TimelineItemsProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $targetVar         = $processorConfiguration['as'] ?? 'items';
        $contentElementUid = (int)($processedData['data']['uid'] ?? 0);

        if ($contentElementUid === 0) {
            $processedData[$targetVar] = [];
            $processedData['settings'] = $this->defaultSettings();
            return $processedData;
        }

        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $flexData = $flexFormService->convertFlexFormContentToArray(
            $processedData['data']['tx_conn_elements_timeline_settings'] ?? ''
        );
        $settings = $this->resolveSettings($flexData['settings'] ?? []);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_timeline_item');

        $rows = $queryBuilder
            ->select('*')
            ->from('tx_conn_elements_domain_model_timeline_item')
            ->where(
                $queryBuilder->expr()->eq(
                    'content_element',
                    $queryBuilder->createNamedParameter($contentElementUid, ParameterType::INTEGER)
                ),
                $queryBuilder->expr()->eq(
                    'deleted',
                    $queryBuilder->createNamedParameter(0, ParameterType::INTEGER)
                ),
                $queryBuilder->expr()->eq(
                    'hidden',
                    $queryBuilder->createNamedParameter(0, ParameterType::INTEGER)
                )
            )
            ->orderBy('sorting', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        if ($settings['sortOrder'] === 'date_asc') {
            usort($rows, fn($a, $b) => ($a['date'] ?? 0) <=> ($b['date'] ?? 0));
        } elseif ($settings['sortOrder'] === 'date_desc') {
            usort($rows, fn($a, $b) => ($b['date'] ?? 0) <=> ($a['date'] ?? 0));
        }

        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        $lastYear  = null;
        $sideIndex = 0;
        foreach ($rows as &$row) {
            try {
                $files = $fileRepository->findByRelation(
                    'tx_conn_elements_domain_model_timeline_item',
                    'image',
                    $row['uid']
                );
                $row['imageFile'] = $files[0] ?? null;
            } catch (\Exception $e) {
                $row['imageFile'] = null;
            }
            $ts = $row['date'] !== null ? (int)$row['date'] : null;
            $row['dateFormatted'] = $ts ? date('d.m.Y', $ts) : '';
            $row['dateYear']      = $ts ? date('Y', $ts) : '';
            $row['isNewYear']     = ($row['dateYear'] !== $lastYear);
            $row['scrollSide']    = ($sideIndex % 2 === 0) ? 'left' : 'right';
            $lastYear  = $row['dateYear'];
            $sideIndex++;
        }
        unset($row);

        $processedData[$targetVar] = $rows;
        $processedData['settings'] = $settings;

        return $processedData;
    }

    private function resolveSettings(array $raw): array
    {
        return [
            'layout'       => in_array($raw['layout'] ?? '', ['changelog', 'responsive', 'scroll'], true)
                                  ? $raw['layout']
                                  : 'changelog',
            'position'     => in_array($raw['position'] ?? '', ['left', 'center', 'right'], true)
                                  ? $raw['position']
                                  : 'left',
            'trackAlign'   => in_array($raw['track_align'] ?? '', ['left', 'center', 'right'], true)
                                  ? $raw['track_align']
                                  : 'center',
            'sortOrder'     => in_array($raw['sort_order'] ?? '', ['custom', 'date_asc', 'date_desc'], true)
                                   ? $raw['sort_order']
                                   : 'custom',
            'showItemDate'  => (bool)($raw['show_item_date'] ?? false),
            'lineStyle'     => in_array($raw['line_style'] ?? '', ['solid', 'dashed', 'dotted'], true)
                                   ? $raw['line_style']
                                   : 'solid',
        ];
    }

    private function defaultSettings(): array
    {
        return [
            'layout'      => 'changelog',
            'position'    => 'left',
            'trackAlign'  => 'center',
            'sortOrder'    => 'custom',
            'showItemDate' => false,
            'lineStyle'    => 'solid',
        ];
    }
}
