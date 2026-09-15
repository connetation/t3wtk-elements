<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

use Connetation\Base\DataProcessing\AbstractItemsProcessor;

final class TimelineItemsProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_elements_domain_model_timeline_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_elements_timeline_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function postProcessRows(array $rows, array $settings): array
    {
        if ($settings['sortOrder'] === 'date_asc') {
            usort($rows, fn($a, $b) => ($a['date'] ?? 0) <=> ($b['date'] ?? 0));
        } elseif ($settings['sortOrder'] === 'date_desc') {
            usort($rows, fn($a, $b) => ($b['date'] ?? 0) <=> ($a['date'] ?? 0));
        }

        $lastYear  = null;
        $sideIndex = 0;
        foreach ($rows as &$row) {
            $ts = $row['date'] !== null ? (int)$row['date'] : null;
            $row['dateFormatted'] = $ts
                ? (($row['date_precision'] ?? 'full') === 'year' ? date('Y', $ts) : date('d.m.Y', $ts))
                : '';
            $row['dateYear']      = $ts ? date('Y', $ts) : '';
            $row['isNewYear']     = ($row['dateYear'] !== $lastYear);
            $row['scrollSide']    = ($sideIndex % 2 === 0) ? 'left' : 'right';
            $lastYear  = $row['dateYear'];
            $sideIndex++;
        }
        unset($row);

        return $rows;
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        return [
            'layout'       => in_array($raw['layout'] ?? '', ['changelog', 'responsive', 'scroll'], true)
                                  ? $raw['layout']
                                  : 'changelog',
            'position'     => in_array($raw['position'] ?? '', ['left', 'center', 'right'], true)
                                  ? $raw['position']
                                  : 'left',
            'trackAlign'   => in_array($raw['track_align'] ?? '', ['left', 'center', 'right'], true)
                                  ? $raw['track_align']
                                  : 'center',
            'sortOrder'     => in_array($raw['sort_order'] ?? '', ['custom', 'date_asc', 'date_desc'], true)
                                   ? $raw['sort_order']
                                   : 'custom',
            'showItemDate'  => (bool)($raw['show_item_date'] ?? false),
            'lineStyle'     => in_array($raw['line_style'] ?? '', ['solid', 'dashed', 'dotted'], true)
                                   ? $raw['line_style']
                                   : 'solid',
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'layout'      => 'changelog',
            'position'    => 'left',
            'trackAlign'  => 'center',
            'sortOrder'    => 'custom',
            'showItemDate' => false,
            'lineStyle'    => 'solid',
        ];
    }
}
