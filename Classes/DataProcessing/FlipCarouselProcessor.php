<?php

declare(strict_types=1);

namespace Connetation\T3kitElements\DataProcessing;

use Connetation\Base\DataProcessing\AbstractItemsProcessor;

final class FlipCarouselProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_t3kit_elements_domain_model_flip_carousel_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3kit_elements_flip_carousel_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function finalizeProcessedData(
        array $processedData,
        array $rows,
        array $settings,
        string $settingsField,
        int $contentElementUid
    ): array {
        // ─── Collect unique categories for filter buttons ─────────
        $categories = [];
        foreach ($rows as $row) {
            $cat = trim($row['category'] ?? '');
            if ($cat !== '' && !in_array($cat, $categories, true)) {
                $categories[] = $cat;
            }
        }

        $processedData['categories'] = $categories;

        return $processedData;
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $columns = (int)($raw['columns'] ?? 4);

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
            'columns'       => max(2, min(4, $columns)),
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
            'columns'       => 4,
            'textPosition'  => 'bottom',
            'textAlign'     => 'left',
            'navPosition'   => 'bottom',
            'autoplay'      => false,
            'autoplay_delay' => 4000,
        ];
    }
}
