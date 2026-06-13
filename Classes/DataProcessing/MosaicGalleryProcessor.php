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

final class MosaicGalleryProcessor implements DataProcessorInterface
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

        // ─── FlexForm settings ────────────────────────────────────────
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $flexData = $flexFormService->convertFlexFormContentToArray(
            $processedData['data']['tx_conn_elements_mosaic_gallery_settings'] ?? ''
        );
        $settings = $this->resolveSettings($flexData['settings'] ?? []);

        // ─── Gallery items ────────────────────────────────────────────
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_mosaic_item');

        $rows = $queryBuilder
            ->select('*')
            ->from('tx_conn_elements_domain_model_mosaic_item')
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

        // ─── Resolve FAL image per item ───────────────────────────────
        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        foreach ($rows as &$row) {
            try {
                $files = $fileRepository->findByRelation(
                    'tx_conn_elements_domain_model_mosaic_item',
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
        $allowedLayouts  = ['masonry', 'justified', 'metro', 'grid'];
        $allowedColumns  = [2, 3, 4, 5];
        $allowedHeights  = [200, 280, 360, 480];
        $allowedGaps     = ['none', 'small', 'medium', 'large'];
        $allowedHover    = ['none', 'zoom', 'dim', 'overlay'];
        $allowedCaption  = ['overlay', 'below'];
        $allowedWidths   = [400, 600, 800, 1200];
        $allowedRadius   = ['none', 'small', 'medium', 'large', 'pill'];
        $allowedAspects  = ['none', '1/1', '4/3', '16/9', '3/4'];

        // Backward compat: old instances without layoutType derive it from aspectRatio
        $layoutType = $raw['layoutType'] ?? '';
        if (empty($layoutType) || !in_array($layoutType, $allowedLayouts, true)) {
            $layoutType = (($raw['aspectRatio'] ?? 'none') !== 'none') ? 'grid' : 'masonry';
        }

        $columns       = $layoutType === 'metro' ? 3 : (
            in_array((int)($raw['columns'] ?? 3), $allowedColumns, true)
                ? (int)($raw['columns'] ?? 3) : 3
        );
        $galleryHeight = in_array((int)($raw['galleryHeight'] ?? 280), $allowedHeights, true)
            ? (int)($raw['galleryHeight'] ?? 280) : 280;
        $gap           = in_array($raw['gap'] ?? 'medium', $allowedGaps, true)
            ? ($raw['gap'] ?? 'medium') : 'medium';
        $hoverEffect   = in_array($raw['hoverEffect'] ?? 'zoom', $allowedHover, true)
            ? ($raw['hoverEffect'] ?? 'zoom') : 'zoom';
        $captionStyle  = in_array($raw['captionStyle'] ?? 'overlay', $allowedCaption, true)
            ? ($raw['captionStyle'] ?? 'overlay') : 'overlay';
        $imageWidth    = in_array((int)($raw['imageWidth'] ?? 800), $allowedWidths, true)
            ? (int)($raw['imageWidth'] ?? 800) : 800;
        $borderRadius  = in_array($raw['borderRadius'] ?? 'medium', $allowedRadius, true)
            ? ($raw['borderRadius'] ?? 'medium') : 'medium';
        $aspectRatio   = in_array($raw['aspectRatio'] ?? 'none', $allowedAspects, true)
            ? ($raw['aspectRatio'] ?? 'none') : 'none';

        return [
            'layoutType'     => $layoutType,
            'columns'        => $columns,
            'galleryHeight'  => $galleryHeight,
            'gap'            => $gap,
            'hoverEffect'    => $hoverEffect,
            'captionStyle'   => $captionStyle,
            'enableLightbox' => (bool)($raw['enableLightbox'] ?? true),
            'showCaptions'   => (bool)($raw['showCaptions'] ?? true),
            'imageWidth'     => $imageWidth,
            'borderRadius'   => $borderRadius,
            'aspectRatio'    => $aspectRatio,
        ];
    }

    private function defaultSettings(): array
    {
        return [
            'layoutType'     => 'masonry',
            'columns'        => 3,
            'galleryHeight'  => 280,
            'gap'            => 'medium',
            'hoverEffect'    => 'zoom',
            'captionStyle'   => 'overlay',
            'enableLightbox' => true,
            'showCaptions'   => true,
            'imageWidth'     => 800,
            'borderRadius'   => 'medium',
            'aspectRatio'    => 'none',
        ];
    }
}
