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

final class LogoItemsProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $targetVar         = $processorConfiguration['as'] ?? 'logos';
        $contentElementUid = (int)($processedData['data']['uid'] ?? 0);

        if ($contentElementUid === 0) {
            $processedData[$targetVar] = [];
            $processedData['settings'] = $this->defaultSettings();
            return $processedData;
        }

        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $flexData = $flexFormService->convertFlexFormContentToArray(
            $processedData['data']['tx_conn_elements_logo_wall_settings'] ?? ''
        );
        $settings = $this->resolveSettings($flexData['settings'] ?? []);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_logo_item');

        $rows = $queryBuilder
            ->select('*')
            ->from('tx_conn_elements_domain_model_logo_item')
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

        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        foreach ($rows as &$row) {
            try {
                $files = $fileRepository->findByRelation(
                    'tx_conn_elements_domain_model_logo_item',
                    'image',
                    $row['uid']
                );
                $row['imageFile'] = $files[0] ?? null;
            } catch (\Exception $e) {
                $row['imageFile'] = null;
            }
        }
        unset($row);

        $processedData[$targetVar] = $rows;
        $processedData['settings'] = $settings;

        return $processedData;
    }

    private function resolveSettings(array $raw): array
    {
        $columns = (int)($raw['columns'] ?? 5);
        $colMap  = [
            3 => 'col-6 col-sm-4',
            4 => 'col-6 col-sm-3',
            5 => 'col-6 col-sm-4 col-lg-auto',
            6 => 'col-6 col-sm-4 col-lg-2',
        ];
        $layout = in_array($raw['layout'] ?? 'grid', ['grid', 'marquee'], true)
            ? ($raw['layout'] ?? 'grid')
            : 'grid';
        $direction = in_array($raw['direction'] ?? 'left', ['left', 'right'], true)
            ? ($raw['direction'] ?? 'left')
            : 'left';

        return [
            'layout'     => $layout,
            'columns'    => $columns,
            'colClass'   => $colMap[$columns] ?? $colMap[5],
            'speed'      => max(10, (int)($raw['speed'] ?? 30)),
            'direction'  => $direction,
            'grayscale'  => (bool)($raw['grayscale'] ?? true),
            'accentBar'  => (bool)($raw['accentBar'] ?? true),
        ];
    }

    private function defaultSettings(): array
    {
        return [
            'layout'    => 'grid',
            'columns'   => 5,
            'colClass'  => 'col-6 col-sm-4 col-lg-auto',
            'speed'     => 30,
            'direction' => 'left',
            'grayscale' => true,
            'accentBar' => true,
        ];
    }
}
