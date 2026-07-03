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

final class FeatureItemsProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $targetVar         = $processorConfiguration['as'] ?? 'features';
        $contentElementUid = (int)($processedData['data']['uid'] ?? 0);

        if ($contentElementUid === 0) {
            $processedData[$targetVar] = [];
            $processedData['settings'] = $this->defaultSettings();
            return $processedData;
        }

        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $flexData = $flexFormService->convertFlexFormContentToArray(
            $processedData['data']['tx_conn_elements_feature_grid_settings'] ?? ''
        );
        $settings = $this->resolveSettings($flexData['settings'] ?? []);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_feature_item');

        $rows = $queryBuilder
            ->select('*')
            ->from('tx_conn_elements_domain_model_feature_item')
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
            $row['imageFile'] = null;
            if ((int)($row['image'] ?? 0) > 0) {
                try {
                    $files = $fileRepository->findByRelation(
                        'tx_conn_elements_domain_model_feature_item',
                        'image',
                        $row['uid']
                    );
                    $row['imageFile'] = $files[0] ?? null;
                } catch (\Exception $e) {
                    // imageFile stays null
                }
            }
        }
        unset($row);

        $processedData[$targetVar] = $rows;
        $processedData['settings'] = $settings;

        return $processedData;
    }

    private function resolveSettings(array $raw): array
    {
        $columns = (int)($raw['columns'] ?? 3);
        $colMap  = [
            2 => 'col-sm-6',
            3 => 'col-sm-6 col-lg-4',
            4 => 'col-sm-6 col-xl-3',
        ];
        $variant = in_array($raw['variant'] ?? 'default', ['default', 'steps', 'horizontal'], true)
            ? ($raw['variant'] ?? 'default')
            : 'default';
        $iconStyle = in_array($raw['iconStyle'] ?? 'soft', ['soft', 'solid', 'none'], true)
            ? ($raw['iconStyle'] ?? 'soft')
            : 'soft';

        return [
            'columns'   => $columns,
            'colClass'  => $colMap[$columns] ?? $colMap[3],
            'variant'   => $variant,
            'iconStyle' => $iconStyle,
            'accentBar' => (bool)($raw['accentBar'] ?? true),
        ];
    }

    private function defaultSettings(): array
    {
        return [
            'columns'   => 3,
            'colClass'  => 'col-sm-6 col-lg-4',
            'variant'   => 'default',
            'iconStyle' => 'soft',
            'accentBar' => true,
        ];
    }
}
