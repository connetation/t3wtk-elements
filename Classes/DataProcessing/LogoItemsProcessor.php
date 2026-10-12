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
use Connetation\T3wtkBase\Utility\ResponsiveValue;

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
        // Per device ("desktop,tablet,mobile"); empty tablet/mobile (0) keeps
        // the element's automatic layout for that screen size.
        $columnsByDevice = ResponsiveValue::parse($raw['columns'] ?? null, 1, 6, 5);
        $columns         = $columnsByDevice['desktop'];
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
            'columnsTablet'=> $columnsByDevice['tablet'] ?? 0,
            'columnsMobile'=> $columnsByDevice['mobile'] ?? 0,
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
            'columnsTablet'=> 0,
            'columnsMobile'=> 0,
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
