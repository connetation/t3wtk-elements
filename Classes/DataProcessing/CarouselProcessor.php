<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

final class CarouselProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_elements_domain_model_carousel_slide';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_elements_carousel_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $allowedRatios = ['21x9', '16x9', '4x3', '1x1'];
        $aspectRatio   = in_array($raw['aspectRatio'] ?? '', $allowedRatios, true)
            ? $raw['aspectRatio'] : '16x9';

        return [
            'aspectRatio' => $aspectRatio,
            'controls'    => (bool)(int)($raw['controls']   ?? 1),
            'indicators'  => (bool)(int)($raw['indicators'] ?? 1),
            'autoplay'    => (bool)(int)($raw['autoplay']   ?? 0),
            'interval'    => max(1000, (int)($raw['interval'] ?? 5000)),
            'fade'        => (bool)(int)($raw['fade']       ?? 0),
            'dark'        => (bool)(int)($raw['dark']       ?? 0),
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'aspectRatio' => '16x9',
            'controls'    => true,
            'indicators'  => true,
            'autoplay'    => false,
            'interval'    => 5000,
            'fade'        => false,
            'dark'        => false,
        ];
    }
}
