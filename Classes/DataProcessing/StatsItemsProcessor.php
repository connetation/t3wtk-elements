<?php

declare(strict_types=1);

namespace Connetation\T3wtkElements\DataProcessing;

use Connetation\T3wtkBase\DataProcessing\AbstractItemsProcessor;

final class StatsItemsProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_t3wtk_elements_domain_model_stat_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'stats';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3wtk_elements_stats_settings';
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $columns = (int)($raw['columns'] ?? 4);
        $colMap  = [
            1 => 'col-12',
            2 => 'col-sm-6',
            3 => 'col-sm-6 col-lg-4',
            4 => 'col-sm-6 col-xl-3',
        ];
        $layout = in_array($raw['layout'] ?? 'default', ['default', 'row'], true)
            ? ($raw['layout'] ?? 'default')
            : 'default';

        return [
            'columns'   => isset($colMap[$columns]) ? $columns : 4,
            'colClass'  => $colMap[$columns] ?? $colMap[4],
            'layout'    => $layout,
            'countup'   => (bool)($raw['countup'] ?? true),
            'duration'  => max(1000, (int)($raw['duration'] ?? 2) * 1000),
            'accentBar' => (bool)($raw['accentBar'] ?? true),
            'showDivider' => (bool)($raw['showDivider'] ?? true),
            'valueColor'       => $this->resolveColor($raw['valueColor'] ?? ''),
            'labelColor'       => $this->resolveColor($raw['labelColor'] ?? ''),
            'descriptionColor' => $this->resolveColor($raw['descriptionColor'] ?? ''),
            'dividerColor'     => $this->resolveColor($raw['dividerColor'] ?? ''),
            'valueShadowColor' => $this->resolveColor($raw['valueShadowColor'] ?? ''),
            'labelShadowColor' => $this->resolveColor($raw['labelShadowColor'] ?? ''),
            'descriptionShadowColor' => $this->resolveColor($raw['descriptionShadowColor'] ?? ''),
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'columns'   => 4,
            'colClass'  => 'col-sm-6 col-xl-3',
            'layout'    => 'default',
            'countup'   => true,
            'duration'  => 2000,
            'accentBar' => true,
            'showDivider' => true,
            'valueColor'       => null,
            'labelColor'       => null,
            'descriptionColor' => null,
            'dividerColor'     => null,
            'valueShadowColor' => null,
            'labelShadowColor' => null,
            'descriptionShadowColor' => null,
        ];
    }

    /**
     * FlexForm 'color' fields with opacity store #RRGGBB or #RRGGBBAA —
     * converted to rgba() (same as ProcessItemsProcessor::resolveColor()).
     * Returns null (fall through to the theme default) when no color was set.
     */
    private function resolveColor(string $hex): ?string
    {
        $hex = ltrim(trim($hex), '#');
        if ($hex === '') {
            return null;
        }
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (!preg_match('/^[0-9a-fA-F]{6}([0-9a-fA-F]{2})?$/', $hex)) {
            return null;
        }

        [$r, $g, $b] = array_map('hexdec', str_split(substr($hex, 0, 6), 2));
        $alpha = strlen($hex) === 8 ? round(hexdec(substr($hex, 6, 2)) / 255, 2) : 1;

        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    }
}
