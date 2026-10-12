<?php

declare(strict_types=1);

/*
 * This file is part of the Connetation TYPO3 Web Toolkit - T3WTK.
 *
 * Copyright (c) Connetation Web Engineering GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
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

use Connetation\T3wtkBase\Utility\RadiusOptions;
use Connetation\T3wtkBase\DataProcessing\AbstractItemsProcessor;

final class TabItemsProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_t3wtk_elements_domain_model_tab_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3wtk_elements_tabs_settings';
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
        $style = in_array($raw['style'] ?? 'tabs', ['tabs', 'connected', 'pills'], true) ? $raw['style'] : 'tabs';
        // "connected" is a nav-tabs variant whose active tab merges into the pane
        $navClass = $style === 'connected' ? 'nav-tabs conn-tabs__nav--connected' : 'nav-' . $style;

        return [
            'style'          => $style,
            'fade'           => !empty($raw['fade'] ?? true),
            'justified'      => !empty($raw['justified']),
            // Legacy records only have the former on/off "roundedCorners" toggle.
            'borderRadius'   => RadiusOptions::resolve(
                $raw['borderRadius'] ?? null,
                !empty($raw['roundedCorners']) ? RadiusOptions::THEME_DEFAULT : 'none'
            ),
            'navClass'       => $navClass . (!empty($raw['justified']) ? ' nav-justified' : ''),
            'paneClass'      => !empty($raw['fade'] ?? true) ? 'fade' : '',
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'style'          => 'tabs',
            'fade'           => true,
            'justified'      => false,
            'borderRadius'   => RadiusOptions::THEME_DEFAULT,
            'navClass'       => 'nav-tabs',
            'paneClass'      => 'fade',
        ];
    }
}
