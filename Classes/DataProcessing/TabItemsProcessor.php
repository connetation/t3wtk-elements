<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

use Connetation\Base\DataProcessing\AbstractItemsProcessor;

final class TabItemsProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_elements_domain_model_tab_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_elements_tabs_settings';
    }

    protected function postProcessRows(array $rows, array $settings): array
    {
        // First item is the one shown/selected on load
        foreach ($rows as $index => &$row) {
            $row['active'] = $index === 0;
        }
        unset($row);

        return $rows;
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $style = ($raw['style'] ?? 'tabs') === 'pills' ? 'pills' : 'tabs';

        return [
            'style'          => $style,
            'fade'           => !empty($raw['fade'] ?? true),
            'justified'      => !empty($raw['justified']),
            'roundedCorners' => !empty($raw['roundedCorners']),
            'navClass'       => 'nav-' . $style . (!empty($raw['justified']) ? ' nav-justified' : ''),
            'paneClass'      => !empty($raw['fade'] ?? true) ? 'fade' : '',
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'style'          => 'tabs',
            'fade'           => true,
            'justified'      => false,
            'roundedCorners' => false,
            'navClass'       => 'nav-tabs',
            'paneClass'      => 'fade',
        ];
    }
}
