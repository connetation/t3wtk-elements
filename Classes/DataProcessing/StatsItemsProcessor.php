<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

final class StatsItemsProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_elements_domain_model_stat_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'stats';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_elements_stats_settings';
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $columns = (int)($raw['columns'] ?? 4);
        $colMap  = [
            2 => 'col-sm-6',
            3 => 'col-sm-6 col-lg-4',
            4 => 'col-sm-6 col-xl-3',
        ];
        $layout = in_array($raw['layout'] ?? 'default', ['default', 'row'], true)
            ? ($raw['layout'] ?? 'default')
            : 'default';

        return [
            'columns'   => $columns,
            'colClass'  => $colMap[$columns] ?? $colMap[4],
            'layout'    => $layout,
            'countup'   => (bool)($raw['countup'] ?? true),
            'duration'  => max(1000, (int)($raw['duration'] ?? 2) * 1000),
            'accentBar' => (bool)($raw['accentBar'] ?? true),
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
        ];
    }
}
