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

final class FlipCarouselProcessor implements DataProcessorInterface
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
            $processedData['data']['tx_conn_elements_flip_carousel_settings'] ?? ''
        );
        $settings = $this->resolveSettings($flexData['settings'] ?? []);

        // ─── Carousel items ───────────────────────────────────────
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_flip_carousel_item');

        $rows = $queryBuilder
            ->select('*')
            ->from('tx_conn_elements_domain_model_flip_carousel_item')
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

        // ─── Resolve FAL image per item ───────────────────────────
        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        foreach ($rows as &$row) {
            try {
                $files = $fileRepository->findByRelation(
                    'tx_conn_elements_domain_model_flip_carousel_item',
                    'image',
                    $row['uid']
                );
                $row['imageFile'] = $files[0] ?? null;
            } catch (\Exception $e) {
                $row['imageFile'] = null;
            }
        }
        unset($row);

        // ─── Collect unique categories for filter buttons ─────────
        $categories = [];
        foreach ($rows as $row) {
            $cat = trim($row['category'] ?? '');
            if ($cat !== '' && !in_array($cat, $categories, true)) {
                $categories[] = $cat;
            }
        }

        $processedData[$targetVar]    = $rows;
        $processedData['settings']    = $settings;
        $processedData['categories']  = $categories;

        return $processedData;
    }

    private function resolveSettings(array $raw): array
    {
        $columns = (int)($raw['columns'] ?? 4);

        $allowedPositions    = ['bottom', 'center', 'top'];
        $allowedAligns       = ['left', 'center', 'right'];
        $allowedNavPositions = ['bottom', 'sides', 'none'];

        $textPosition = in_array($raw['textPosition'] ?? '', $allowedPositions, true)
            ? $raw['textPosition']
            : 'bottom';
        $textAlign = in_array($raw['textAlign'] ?? '', $allowedAligns, true)
            ? $raw['textAlign']
            : 'left';
        $navPosition = in_array($raw['navPosition'] ?? '', $allowedNavPositions, true)
            ? $raw['navPosition']
            : 'bottom';

        return [
            'columns'      => max(2, min(4, $columns)),
            'textPosition' => $textPosition,
            'textAlign'    => $textAlign,
            'navPosition'  => $navPosition,
        ];
    }

    private function defaultSettings(): array
    {
        return [
            'columns'      => 4,
            'textPosition' => 'bottom',
            'textAlign'    => 'left',
            'navPosition'  => 'bottom',
        ];
    }
}
