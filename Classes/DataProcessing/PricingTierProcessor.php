<?php

declare(strict_types=1);

namespace Connetation\T3wtkElements\DataProcessing;

use Connetation\T3wtkBase\DataProcessing\AbstractItemsProcessor;

final class PricingTierProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_t3wtk_elements_domain_model_pricing_tier';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'tiers';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3wtk_elements_pricing_settings';
    }

    protected function postProcessRows(array $rows, array $settings): array
    {
        // Split features textarea; detect unavailable items (prefixed with -)
        foreach ($rows as &$row) {
            $raw = trim($row['features'] ?? '');
            $featureList = [];
            if ($raw !== '') {
                foreach (explode("\n", $raw) as $line) {
                    $line = trim($line);
                    if ($line === '') continue;
                    $unavailable = str_starts_with($line, '-');
                    $featureList[] = [
                        'text'        => $unavailable ? trim(substr($line, 1)) : $line,
                        'unavailable' => $unavailable,
                    ];
                }
            }
            $row['featureList'] = $featureList;
            $row['featured']    = (bool)$row['featured'];
        }
        unset($row);

        return $rows;
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $columns = (int)($raw['columns'] ?? 3);
        $colMap  = [
            2 => 'col-sm-6',
            3 => 'col-sm-6 col-lg-4',
            4 => 'col-sm-6 col-xl-3',
        ];
        $style = in_array($raw['style'] ?? 'default', ['default', 'shadow', 'flat'], true)
            ? ($raw['style'] ?? 'default')
            : 'default';

        return [
            'columns'   => $columns,
            'colClass'  => $colMap[$columns] ?? $colMap[3],
            'style'     => $style,
            'accentBar' => (bool)($raw['accentBar'] ?? true),
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'columns'   => 3,
            'colClass'  => 'col-sm-6 col-lg-4',
            'style'     => 'default',
            'accentBar' => true,
        ];
    }
}
