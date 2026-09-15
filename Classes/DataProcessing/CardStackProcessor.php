<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

use Connetation\Base\DataProcessing\AbstractItemsProcessor;

final class CardStackProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_elements_domain_model_stack_card';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_elements_card_stack_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function postProcessRows(array $rows, array $settings): array
    {
        // Reverse so that CMS item #1 (sorting=1) becomes last-child = front card
        return array_reverse($rows);
    }

    protected function resolveSettings(array $raw, string $settingsField): array
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

    protected function defaultSettings(string $settingsField): array
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
