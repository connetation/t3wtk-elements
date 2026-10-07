<?php

declare(strict_types=1);

namespace Connetation\T3wtkElements\DataProcessing;

use Connetation\T3wtkBase\DataProcessing\AbstractItemsProcessor;
use Connetation\T3wtkBase\Utility\ResponsiveValue;
use Connetation\T3wtkBase\Utility\FontWeightOptions;

final class FeatureItemsProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_t3wtk_elements_domain_model_feature_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'features';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3wtk_elements_feature_grid_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        // Per device ("desktop,tablet,mobile"); empty tablet/mobile (0) keeps
        // the element's automatic layout for that screen size.
        $columnsByDevice = ResponsiveValue::parse($raw['columns'] ?? null, 1, 4, 3);
        $columns         = $columnsByDevice['desktop'];
        $colMap  = [
            2 => 'col-sm-6',
            3 => 'col-sm-6 col-lg-4',
            4 => 'col-sm-6 col-xl-3',
        ];
        $variant = in_array($raw['variant'] ?? 'default', ['default', 'steps', 'horizontal'], true)
            ? ($raw['variant'] ?? 'default')
            : 'default';
        $iconStyle = in_array($raw['iconStyle'] ?? 'soft', ['soft', 'solid', 'none'], true)
            ? ($raw['iconStyle'] ?? 'soft')
            : 'soft';

        return [
            'columns'   => $columns,
            'columnsTablet'=> $columnsByDevice['tablet'] ?? 0,
            'columnsMobile'=> $columnsByDevice['mobile'] ?? 0,
            'colClass'  => $colMap[$columns] ?? $colMap[3],
            'variant'   => $variant,
            'iconStyle' => $iconStyle,
            'accentBar' => (bool)($raw['accentBar'] ?? true),
        ] + FontWeightOptions::resolveCard($raw);
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'columns'   => 3,
            'columnsTablet'=> 0,
            'columnsMobile'=> 0,
            'colClass'  => 'col-sm-6 col-lg-4',
            'variant'   => 'default',
            'iconStyle' => 'soft',
            'accentBar' => true,
        ] + FontWeightOptions::CARD_DEFAULTS;
    }
}
