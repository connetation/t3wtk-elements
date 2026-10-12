<?php

declare(strict_types=1);

/*
 * This file is part of the Connetation TYPO3 Web Toolkit - T3WTK.
 *
 * Copyright (c) Connetation Web Engineering GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Connetation\T3wtkElements\DataProcessing;

use Connetation\T3wtkBase\DataProcessing\AbstractItemsProcessor;

final class ProcessItemsProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_t3wtk_elements_domain_model_process_step';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3wtk_elements_process_settings';
    }

    protected function postProcessRows(array $rows, array $settings): array
    {
        foreach ($rows as $index => &$row) {
            $row['number'] = $index + 1;
            // A whitespace-only icon field (e.g. an accidental stray space
            // typed in the backend) would otherwise pass Fluid's truthiness
            // check and render an empty <i> tag instead of falling back to
            // the step number.
            $row['icon'] = trim((string)($row['icon'] ?? ''));
        }
        unset($row);

        return $rows;
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $connectorStyle = $raw['connectorStyle'] ?? 'solid';

        $maxPerRow = (int)($raw['maxPerRow'] ?? 0);

        return [
            'numbered'          => !empty($raw['numbered'] ?? true),
            'connectorStyle'    => in_array($connectorStyle, ['none', 'solid', 'dashed', 'dotted'], true) ? $connectorStyle : 'solid',
            'maxPerRow'         => in_array($maxPerRow, [2, 3, 4, 5, 6], true) ? $maxPerRow : 0,
            'connectorColor'    => $this->resolveColor($raw['connectorColor'] ?? ''),
            'markerColor'       => $this->resolveColor($raw['markerColor'] ?? ''),
            'markerBorderColor' => $this->resolveColor($raw['markerBorderColor'] ?? ''),
            'markerNumberColor' => $this->resolveColor($raw['markerNumberColor'] ?? ''),
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'numbered'          => true,
            'connectorStyle'    => 'solid',
            'maxPerRow'         => 0,
            'connectorColor'    => null,
            'markerColor'       => null,
            'markerBorderColor' => null,
            'markerNumberColor' => null,
        ];
    }

    /**
     * TCA/FlexForm 'color' fields with 'opacity' => true store an 8-digit
     * #RRGGBBAA hex (the picker's own alpha slider) instead of the usual
     * 6-digit #RRGGBB — converts either into a plain rgba() string, done
     * here rather than in CSS (e.g. color-mix()) so it degrades to a
     * universally-supported color value. Returns null (fall through to the
     * theme default) when no color was set.
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
