<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

final class AccordionItemsProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_elements_domain_model_accordion_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_elements_accordion_settings';
    }

    protected function postProcessRows(array $rows, array $settings): array
    {
        // Mark which item should be expanded on load
        foreach ($rows as $index => &$row) {
            $row['open'] = ($index === 0) && !$settings['collapseAll'];
        }
        unset($row);

        return $rows;
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        return [
            'style'          => ($raw['style'] ?? 'default') === 'flush' ? 'flush' : 'default',
            'alwaysOpen'     => !empty($raw['alwaysOpen']),
            'collapseAll'    => !empty($raw['collapseAll']),
            'accordionClass' => 'accordion' . (($raw['style'] ?? '') === 'flush' ? ' accordion-flush' : ''),
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'style'          => 'default',
            'alwaysOpen'     => false,
            'collapseAll'    => false,
            'accordionClass' => 'accordion',
        ];
    }
}
