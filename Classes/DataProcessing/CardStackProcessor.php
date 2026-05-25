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

final class CardStackProcessor implements DataProcessorInterface
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
            $processedData['data']['tx_conn_elements_card_stack_settings'] ?? ''
        );
        $settings = $this->resolveSettings($flexData['settings'] ?? []);

        // ─── Stack items ──────────────────────────────────────────
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_stack_card');

        $rows = $queryBuilder
            ->select('*')
            ->from('tx_conn_elements_domain_model_stack_card')
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

        // ─── Resolve FAL image per card ───────────────────────────
        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        foreach ($rows as &$row) {
            try {
                $files = $fileRepository->findByRelation(
                    'tx_conn_elements_domain_model_stack_card',
                    'image',
                    $row['uid']
                );
                $row['imageFile'] = $files[0] ?? null;
            } catch (\Exception $e) {
                $row['imageFile'] = null;
            }
        }
        unset($row);

        // Reverse so that CMS item #1 (sorting=1) becomes last-child = front card
        $rows = array_reverse($rows);

        $processedData[$targetVar] = $rows;
        $processedData['settings'] = $settings;

        return $processedData;
    }

    private function resolveSettings(array $raw): array
    {
        $allowedPositions    = ['bottom', 'center', 'top'];
        $allowedAligns       = ['left', 'center', 'right'];
        $allowedNavPositions = ['bottom', 'sides', 'none'];
        $allowedDelays       = [3000, 4000, 5000, 7000];

        $textPosition = in_array($raw['textPosition'] ?? '', $allowedPositions, true)
            ? $raw['textPosition']
            : 'bottom';
        $textAlign = in_array($raw['textAlign'] ?? '', $allowedAligns, true)
            ? $raw['textAlign']
            : 'left';
        $navPosition = in_array($raw['navPosition'] ?? '', $allowedNavPositions, true)
            ? $raw['navPosition']
            : 'bottom';
        $autoplay = (bool)($raw['autoplay'] ?? false);
        $autoplayDelay = (int)($raw['autoplay_delay'] ?? 4000);
        if (!in_array($autoplayDelay, $allowedDelays, true)) {
            $autoplayDelay = 4000;
        }

        return [
            'textPosition'  => $textPosition,
            'textAlign'     => $textAlign,
            'navPosition'   => $navPosition,
            'autoplay'      => $autoplay,
            'autoplay_delay' => $autoplayDelay,
        ];
    }

    private function defaultSettings(): array
    {
        return [
            'textPosition'  => 'bottom',
            'textAlign'     => 'left',
            'navPosition'   => 'bottom',
            'autoplay'      => false,
            'autoplay_delay' => 4000,
        ];
    }
}
