<?php

declare(strict_types=1);

namespace Connetation\T3kitElements\DataProcessing;

use Connetation\T3kitBase\Utility\RadiusOptions;
use Connetation\T3kitBase\DataProcessing\AbstractItemsProcessor;

final class CarouselProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_t3kit_elements_domain_model_carousel_slide';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3kit_elements_carousel_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $allowedRatios = ['21x9', '16x9', '4x3', '1x1', 'original'];
        $aspectRatio   = in_array($raw['aspectRatio'] ?? '', $allowedRatios, true)
            ? $raw['aspectRatio'] : '16x9';

        $allowedOriginalHeights = ['auto', '55vh', '70vh', '100vh'];
        $originalHeightRaw = $raw['originalHeight'] ?? 'auto';
        $originalHeight = in_array($originalHeightRaw, $allowedOriginalHeights, true)
            ? $originalHeightRaw : 'auto';

        $allowedStyles = ['none', 'shadow', 'border'];
        $styleRaw = $raw['style'] ?? 'none';
        $style = in_array($styleRaw, $allowedStyles, true) ? $styleRaw : 'none';

        // Output as data-radius; sizes come from conn/t3kit-base's conn-radius-option() mixin.
        $borderRadius = RadiusOptions::resolve($raw['borderRadius'] ?? null);

        return [
            'aspectRatio'       => $aspectRatio,
            'originalHeight'    => $originalHeight,
            'style'             => $style,
            'borderRadius'      => $borderRadius,
            'controls'          => (bool)(int)($raw['controls']   ?? 1),
            'indicators'        => (bool)(int)($raw['indicators'] ?? 1),
            'autoplay'          => (bool)(int)($raw['autoplay']   ?? 0),
            'interval'          => max(1000, (int)($raw['interval'] ?? 5000)),
            'fade'              => (bool)(int)($raw['fade']       ?? 0),
            'dark'              => (bool)(int)($raw['dark']       ?? 0),
            'openInLightbox'    => (bool)(int)($raw['openInLightbox'] ?? 0),
            'captionTitleColor' => $this->sanitizeHexColor($raw['captionTitleColor'] ?? '', '#ffffff'),
            'captionTextColor'  => $this->sanitizeHexColor($raw['captionTextColor'] ?? '', '#ffffff'),
            'captionBgColor'    => $this->sanitizeHexColor($raw['captionBgColor'] ?? '', '#00000094'),
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'aspectRatio'       => '16x9',
            'originalHeight'    => 'auto',
            'style'             => 'none',
            'borderRadius'      => RadiusOptions::THEME_DEFAULT,
            'controls'          => true,
            'indicators'        => true,
            'autoplay'          => false,
            'interval'          => 5000,
            'fade'              => false,
            'dark'              => false,
            'openInLightbox'    => false,
            'captionTitleColor' => '#ffffff',
            'captionTextColor'  => '#ffffff',
            'captionBgColor'    => '#00000094',
        ];
    }
}
