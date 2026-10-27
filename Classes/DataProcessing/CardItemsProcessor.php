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

use Connetation\T3wtkBase\DataProcessing\AbstractItemsProcessor;
use Connetation\T3wtkBase\Utility\ResponsiveValue;
use Connetation\T3wtkBase\Utility\CardAlignOptions;
use Connetation\T3wtkBase\Utility\FontWeightOptions;

final class CardItemsProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_t3wtk_elements_domain_model_card_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'cards';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3wtk_elements_card_group_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        // Per device ("desktop,tablet,mobile"); a former single value is desktop only.
        $columns = ResponsiveValue::parse($raw['columns'] ?? null, 1, 4, 3);
        $style = in_array($raw['style'] ?? 'default', ['default', 'shadow', 'flat'], true)
            ? ($raw['style'] ?? 'default')
            : 'default';
        $bodyPosition = in_array($raw['bodyPosition'] ?? 'top', ['top', 'center', 'bottom'], true)
            ? ($raw['bodyPosition'] ?? 'top')
            : 'top';

        return [
            'columns'      => $columns['desktop'],
            'colClass'     => '',
            'rowColsClass' => ResponsiveValue::rowColsClass($columns),
            'style'        => $style,
            'accentBar'    => (bool)($raw['accentBar'] ?? true),
            'bodyPosition' => $bodyPosition,
        ] + FontWeightOptions::resolveCard($raw) + CardAlignOptions::resolveSettings($raw);
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'columns'      => 3,
            'colClass'     => '',
            'rowColsClass' => ResponsiveValue::rowColsClass(['desktop' => 3, 'tablet' => null, 'mobile' => null]),
            'style'        => 'default',
            'accentBar'    => true,
            'bodyPosition' => 'top',
        ] + FontWeightOptions::CARD_DEFAULTS + CardAlignOptions::DEFAULTS;
    }

    // Records saved before the *_align columns existed have '' rather than
    // the TCA default (only applied to newly created records), which would
    // render a broken "text-" class.
    protected function postProcessRows(array $rows, array $settings): array
    {
        foreach ($rows as &$row) {
            $row['title_align']    = $this->resolveAlign($row['title_align'] ?? null);
            $row['link_align']     = $this->resolveAlign($row['link_align'] ?? null);
            // Subtitle (and eyebrow) alignment: 'auto' follows the title.
            $row['subtitleAlignClass'] = $this->resolveAlignClass($row['subtitle_align'] ?? null, $row['title_align']);
        }
        unset($row);

        return $rows;
    }

    private function resolveAlign(?string $align): string
    {
        return in_array($align, ['start', 'center', 'end'], true) ? $align : 'start';
    }
}
