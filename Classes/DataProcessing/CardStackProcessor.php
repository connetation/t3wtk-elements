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
use Connetation\T3wtkBase\Utility\FontWeightOptions;

final class CardStackProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_t3wtk_elements_domain_model_stack_card';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3wtk_elements_card_stack_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function postProcessRows(array $rows, array $settings): array
    {
        foreach ($rows as &$row) {
            // No per-card title alignment: 'auto' keeps the element's text alignment.
            $row['eyebrowAlignClass'] = $this->resolveAlignClass($row['subtitle_eyebrow_position'] ?? null);
        }
        unset($row);

        // Reverse so that CMS item #1 (sorting=1) becomes last-child = front card
        return array_reverse($rows);
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $allowedPositions    = ['bottom', 'center', 'top'];
        $allowedAligns       = ['left', 'center', 'right'];
        $allowedNavPositions = ['bottom', 'sides', 'none'];
        $allowedDelays       = [3000, 4000, 5000, 7000];

        $textPosition = in_array($raw['textPosition'] ?? '', $allowedPositions, true)
            ? $raw['textPosition']
            : 'bottom';
        $textAlign = in_array($raw['textAlign'] ?? '', $allowedAligns, true)
            ? $raw['textAlign']
            : 'left';
        $navPosition = in_array($raw['navPosition'] ?? '', $allowedNavPositions, true)
            ? $raw['navPosition']
            : 'bottom';
        $autoplay = (bool)($raw['autoplay'] ?? false);
        $autoplayDelay = (int)($raw['autoplay_delay'] ?? 4000);
        if (!in_array($autoplayDelay, $allowedDelays, true)) {
            $autoplayDelay = 4000;
        }

        return [
            'textPosition'  => $textPosition,
            'textAlign'     => $textAlign,
            'navPosition'   => $navPosition,
            'autoplay'      => $autoplay,
            'autoplay_delay' => $autoplayDelay,
        ] + FontWeightOptions::resolveCard($raw);
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'textPosition'  => 'bottom',
            'textAlign'     => 'left',
            'navPosition'   => 'bottom',
            'autoplay'      => false,
            'autoplay_delay' => 4000,
        ] + FontWeightOptions::CARD_DEFAULTS;
    }
}
