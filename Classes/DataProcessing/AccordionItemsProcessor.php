<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

final class AccordionItemsProcessor implements DataProcessorInterface
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

        // ─── FlexForm settings ────────────────────────────────────
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $flexData = $flexFormService->convertFlexFormContentToArray(
            $processedData['data']['tx_conn_elements_accordion_settings'] ?? ''
        );
        $settings = $this->resolveSettings($flexData['settings'] ?? []);

        // ─── Accordion items ──────────────────────────────────────
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_accordion_item');

        $rows = $queryBuilder
            ->select('*')
            ->from('tx_conn_elements_domain_model_accordion_item')
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

        // Mark which item should be expanded on load
        foreach ($rows as $index => &$row) {
            $row['open'] = ($index === 0) && !$settings['collapseAll'];
        }
        unset($row);

        $processedData[$targetVar]   = $rows;
        $processedData['settings']   = $settings;

        return $processedData;
    }

    private function resolveSettings(array $raw): array
    {
        return [
            'style'          => ($raw['style'] ?? 'default') === 'flush' ? 'flush' : 'default',
            'alwaysOpen'     => !empty($raw['alwaysOpen']),
            'collapseAll'    => !empty($raw['collapseAll']),
            'accordionClass' => 'accordion' . (($raw['style'] ?? '') === 'flush' ? ' accordion-flush' : ''),
        ];
    }

    private function defaultSettings(): array
    {
        return [
            'style'          => 'default',
            'alwaysOpen'     => false,
            'collapseAll'    => false,
            'accordionClass' => 'accordion',
        ];
    }
}
