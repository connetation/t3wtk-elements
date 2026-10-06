<?php

declare(strict_types=1);

namespace Connetation\T3wtkElements\DataProcessing;

use Connetation\T3wtkBase\DataProcessing\AbstractItemsProcessor;

final class LogoItemsProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_t3wtk_elements_domain_model_logo_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'logos';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3wtk_elements_logo_wall_settings';
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
        $layout = in_array($raw['layout'] ?? 'grid', ['grid', 'flex', 'marquee'], true)
            ? ($raw['layout'] ?? 'grid')
            : 'grid';
        $itemStyle = in_array($raw['itemStyle'] ?? 'card', ['card', 'plain'], true)
            ? ($raw['itemStyle'] ?? 'card')
            : 'card';
        $justify = in_array($raw['justify'] ?? 'center', ['start', 'center', 'end', 'between', 'around', 'evenly'], true)
            ? ($raw['justify'] ?? 'center')
            : 'center';
        $alignItems = in_array($raw['alignItems'] ?? 'center', ['start', 'center', 'end', 'stretch'], true)
            ? ($raw['alignItems'] ?? 'center')
            : 'center';
        $gap = in_array($raw['gap'] ?? 'md', ['none', 'sm', 'md', 'lg'], true)
            ? ($raw['gap'] ?? 'md')
            : 'md';
        $flexColumns = in_array($raw['flexColumns'] ?? 'auto', ['auto', '2', '3', '4', '5', '6'], true)
            ? ($raw['flexColumns'] ?? 'auto')
            : 'auto';
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
            'itemStyle'  => $itemStyle,
            'justify'    => $justify,
            'alignItems' => $alignItems,
            'wrap'       => (bool)($raw['wrap'] ?? true),
            'gap'        => $gap,
            'flexColumns' => $flexColumns,
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
            'itemStyle' => 'card',
            'justify'   => 'center',
            'alignItems' => 'center',
            'wrap'      => true,
            'gap'       => 'md',
            'flexColumns' => 'auto',
        ];
    }
}
