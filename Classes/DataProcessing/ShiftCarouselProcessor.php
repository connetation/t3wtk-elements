<?php

declare(strict_types=1);

namespace Connetation\T3wtkElements\DataProcessing;

use Connetation\T3wtkBase\DataProcessing\AbstractItemsProcessor;
use Connetation\T3wtkBase\Utility\ResponsiveValue;
use Connetation\T3wtkBase\Utility\FontWeightOptions;

final class ShiftCarouselProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_t3wtk_elements_domain_model_shift_carousel_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3wtk_elements_shift_carousel_settings';
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
        // Per device ("desktop,tablet,mobile"); empty tablet/mobile (0) = automatic.
        $columns = ResponsiveValue::parse($raw['columns'] ?? null, 1, 4, 4);

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
            'columns'       => $columns['desktop'],
            'columnsTablet' => $columns['tablet'] ?? 0,
            'columnsMobile' => $columns['mobile'] ?? 0,
            'textPosition'  => $textPosition,
            'textAlign'     => $textAlign,
            'navPosition'   => $navPosition,
            'autoplay'      => $autoplay,
            'autoplay_delay' => $autoplayDelay,
        ] + FontWeightOptions::resolveCard($raw);
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'columns'       => 4,
            'columnsTablet' => 0,
            'columnsMobile' => 0,
            'textPosition'  => 'bottom',
            'textAlign'     => 'left',
            'navPosition'   => 'bottom',
            'autoplay'      => false,
            'autoplay_delay' => 4000,
        ] + FontWeightOptions::CARD_DEFAULTS;
    }

    protected function postProcessRows(array $rows, array $settings): array
    {
        foreach ($rows as &$row) {
            // No per-item title alignment: 'auto' keeps the element's text alignment.
            $row['eyebrowAlignClass'] = $this->resolveAlignClass($row['subtitle_eyebrow_position'] ?? null);
        }
        unset($row);

        return $rows;
    }
}
