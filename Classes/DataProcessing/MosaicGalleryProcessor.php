<?php

declare(strict_types=1);

namespace Connetation\T3kitElements\DataProcessing;

use Connetation\T3kitBase\Utility\RadiusOptions;
use Connetation\T3kitBase\DataProcessing\AbstractItemsProcessor;

final class MosaicGalleryProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_t3kit_elements_domain_model_mosaic_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3kit_elements_mosaic_gallery_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $allowedLayouts  = ['masonry', 'justified', 'metro', 'grid'];
        $allowedColumns  = [2, 3, 4, 5];
        $allowedHeights  = [200, 280, 360, 480];
        $allowedGaps     = ['none', 'small', 'medium', 'large'];
        $allowedHover    = ['none', 'zoom', 'dim', 'overlay'];
        $allowedCaption  = ['overlay', 'below'];
        $allowedAspects  = ['none', '1/1', '4/3', '16/9', '3/4'];
        $allowedScrim    = ['none', 'light', 'medium', 'strong', 'solid'];
        $allowedPatterns = ['repeat', 'alternate'];

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
        $borderRadius  = RadiusOptions::resolve($raw['borderRadius'] ?? null);
        $aspectRatio   = in_array($raw['aspectRatio'] ?? 'none', $allowedAspects, true)
            ? ($raw['aspectRatio'] ?? 'none') : 'none';
        $captionScrim  = in_array($raw['captionScrim'] ?? 'medium', $allowedScrim, true)
            ? ($raw['captionScrim'] ?? 'medium') : 'medium';
        $metroPattern  = in_array($raw['metroPattern'] ?? 'repeat', $allowedPatterns, true)
            ? ($raw['metroPattern'] ?? 'repeat') : 'repeat';

        return [
            'layoutType'        => $layoutType,
            'metroPattern'      => $metroPattern,
            'columns'           => $columns,
            'galleryHeight'     => $galleryHeight,
            'gap'               => $gap,
            'hoverEffect'       => $hoverEffect,
            'captionStyle'      => $captionStyle,
            'openInLightbox'    => (bool)($raw['openInLightbox'] ?? true),
            'showCaptions'      => (bool)($raw['showCaptions'] ?? true),
            'borderRadius'      => $borderRadius,
            'aspectRatio'       => $aspectRatio,
            'captionScrim'      => $captionScrim,
            // Native color-with-opacity FlexForm fields already emit a ready-to-use
            // #RRGGBBAA string — no conversion needed, just guard against garbage.
            'captionTitleColor' => $this->sanitizeHexColor($raw['captionTitleColor'] ?? '', '#ffffff'),
            'captionTextColor'  => $this->sanitizeHexColor($raw['captionTextColor'] ?? '', '#ffffffd9'),
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'layoutType'        => 'masonry',
            'metroPattern'      => 'repeat',
            'columns'           => 3,
            'galleryHeight'     => 280,
            'gap'               => 'medium',
            'hoverEffect'       => 'zoom',
            'captionStyle'      => 'overlay',
            'openInLightbox'    => true,
            'showCaptions'      => true,
            'borderRadius'      => RadiusOptions::THEME_DEFAULT,
            'aspectRatio'       => 'none',
            'captionScrim'      => 'medium',
            'captionTitleColor' => '#ffffff',
            'captionTextColor'  => '#ffffffd9',
        ];
    }
}
