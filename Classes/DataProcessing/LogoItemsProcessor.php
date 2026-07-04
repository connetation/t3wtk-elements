<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

final class LogoItemsProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_elements_domain_model_logo_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'logos';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_elements_logo_wall_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $columns = (int)($raw['columns'] ?? 5);
        $colMap  = [
            3 => 'col-6 col-sm-4',
            4 => 'col-6 col-sm-3',
            5 => 'col-6 col-sm-4 col-lg-auto',
            6 => 'col-6 col-sm-4 col-lg-2',
        ];
        $layout = in_array($raw['layout'] ?? 'grid', ['grid', 'marquee'], true)
            ? ($raw['layout'] ?? 'grid')
            : 'grid';
        $direction = in_array($raw['direction'] ?? 'left', ['left', 'right'], true)
            ? ($raw['direction'] ?? 'left')
            : 'left';

        return [
            'layout'     => $layout,
            'columns'    => $columns,
            'colClass'   => $colMap[$columns] ?? $colMap[5],
            'speed'      => max(10, (int)($raw['speed'] ?? 30)),
            'direction'  => $direction,
            'grayscale'  => (bool)($raw['grayscale'] ?? true),
            'accentBar'  => (bool)($raw['accentBar'] ?? true),
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'layout'    => 'grid',
            'columns'   => 5,
            'colClass'  => 'col-6 col-sm-4 col-lg-auto',
            'speed'     => 30,
            'direction' => 'left',
            'grayscale' => true,
            'accentBar' => true,
        ];
    }
}
