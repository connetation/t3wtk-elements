<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

final class PricingTierProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $targetVar         = $processorConfiguration['as'] ?? 'tiers';
        $contentElementUid = (int)($processedData['data']['uid'] ?? 0);

        if ($contentElementUid === 0) {
            $processedData[$targetVar] = [];
            $processedData['settings'] = $this->defaultSettings();
            return $processedData;
        }

        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $flexData = $flexFormService->convertFlexFormContentToArray(
            $processedData['data']['tx_conn_elements_pricing_settings'] ?? ''
        );
        $settings = $this->resolveSettings($flexData['settings'] ?? []);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_pricing_tier');

        $rows = $queryBuilder
            ->select('*')
            ->from('tx_conn_elements_domain_model_pricing_tier')
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

        // Split features textarea; detect unavailable items (prefixed with -)
        foreach ($rows as &$row) {
            $raw = trim($row['features'] ?? '');
            $featureList = [];
            if ($raw !== '') {
                foreach (explode("\n", $raw) as $line) {
                    $line = trim($line);
                    if ($line === '') continue;
                    $unavailable = str_starts_with($line, '-');
                    $featureList[] = [
                        'text'        => $unavailable ? trim(substr($line, 1)) : $line,
                        'unavailable' => $unavailable,
                    ];
                }
            }
            $row['featureList'] = $featureList;
            $row['featured']    = (bool)$row['featured'];
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
        $style = in_array($raw['style'] ?? 'default', ['default', 'shadow', 'flat'], true)
            ? ($raw['style'] ?? 'default')
            : 'default';

        return [
            'columns'  => $columns,
            'colClass' => $colMap[$columns] ?? $colMap[3],
            'style'    => $style,
        ];
    }

    private function defaultSettings(): array
    {
        return [
            'columns'  => 3,
            'colClass' => 'col-sm-6 col-lg-4',
            'style'    => 'default',
        ];
    }
}
