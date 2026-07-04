<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

final class FlipGalleryProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_elements_domain_model_gallery_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_elements_flip_gallery_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $columns = (int)($raw['columns'] ?? 3);
        $colMap  = [
            2 => 'col-sm-6',
            3 => 'col-sm-6 col-lg-4',
            4 => 'col-sm-6 col-xl-3',
            6 => 'col-4 col-sm-3 col-md-2',
        ];

        $allowedPositions = ['bottom', 'center', 'top'];
        $allowedAligns    = ['left', 'center', 'right'];

        $textPosition = in_array($raw['textPosition'] ?? '', $allowedPositions, true)
            ? $raw['textPosition']
            : 'bottom';
        $textAlign = in_array($raw['textAlign'] ?? '', $allowedAligns, true)
            ? $raw['textAlign']
            : 'left';

        return [
            'columns'      => $columns,
            'colClass'     => $colMap[$columns] ?? $colMap[3],
            'textPosition' => $textPosition,
            'textAlign'    => $textAlign,
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'columns'      => 3,
            'colClass'     => 'col-sm-6 col-lg-4',
            'textPosition' => 'bottom',
            'textAlign'    => 'left',
        ];
    }
}
