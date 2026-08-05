<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

final class MosaicGalleryProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_elements_domain_model_mosaic_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_elements_mosaic_gallery_settings';
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
        $allowedRadius   = ['none', 'small', 'medium', 'large', 'pill'];
        $allowedAspects  = ['none', '1/1', '4/3', '16/9', '3/4'];
        $allowedScrim    = ['none', 'light', 'medium', 'strong', 'solid'];

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
        $borderRadius  = in_array($raw['borderRadius'] ?? 'medium', $allowedRadius, true)
            ? ($raw['borderRadius'] ?? 'medium') : 'medium';
        $aspectRatio   = in_array($raw['aspectRatio'] ?? 'none', $allowedAspects, true)
            ? ($raw['aspectRatio'] ?? 'none') : 'none';
        $captionScrim  = in_array($raw['captionScrim'] ?? 'medium', $allowedScrim, true)
            ? ($raw['captionScrim'] ?? 'medium') : 'medium';

        return [
            'layoutType'        => $layoutType,
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
            'columns'           => 3,
            'galleryHeight'     => 280,
            'gap'               => 'medium',
            'hoverEffect'       => 'zoom',
            'captionStyle'      => 'overlay',
            'openInLightbox'    => true,
            'showCaptions'      => true,
            'borderRadius'      => 'medium',
            'aspectRatio'       => 'none',
            'captionScrim'      => 'medium',
            'captionTitleColor' => '#ffffff',
            'captionTextColor'  => '#ffffffd9',
        ];
    }

    private function sanitizeHexColor(string $hex, string $default): string
    {
        return preg_match('/^#[0-9a-fA-F]{3,8}$/', $hex) ? $hex : $default;
    }
}
