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
use Connetation\T3wtkBase\Utility\FontWeightOptions;

final class FlipCardItemsProcessor extends AbstractItemsProcessor
{
    private const COLOR_VARS = [
        'bg_color'       => '--conn-flip-card-bg',
        'title_color'    => '--conn-flip-card-title',
        'subtitle_color' => '--conn-flip-card-subtitle',
        'text_color'     => '--conn-flip-card-text',
    ];

    protected function getTable(): string
    {
        return 'tx_conn_t3wtk_elements_domain_model_flip_card_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'cards';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3wtk_elements_flip_card_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile', 'back_image' => 'backImageFile'];
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        // Per device ("desktop,tablet,mobile"); a former single value is desktop only.
        $columns = ResponsiveValue::parse($raw['columns'] ?? null, 1, 4, 3);

        return [
            'columns'      => $columns['desktop'],
            'colClass'     => '',
            'rowColsClass' => ResponsiveValue::rowColsClass($columns),
            'ratio'     => $this->resolveChoice($raw['ratio'] ?? null, ['portrait', 'square', 'landscape']),
            'position'  => $this->resolveChoice($raw['contentPosition'] ?? null, ['bottom', 'center', 'top']),
            'direction' => $this->resolveChoice($raw['direction'] ?? null, ['horizontal', 'vertical']),
            'trigger'   => $this->resolveChoice($raw['trigger'] ?? null, ['hover', 'click']),
            'style'     => $this->resolveChoice($raw['style'] ?? null, ['default', 'shadow', 'flat']),
        ] + FontWeightOptions::resolveCard($raw);
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'columns'      => 3,
            'colClass'     => '',
            'rowColsClass' => ResponsiveValue::rowColsClass(['desktop' => 3, 'tablet' => null, 'mobile' => null]),
            'ratio'     => 'portrait',
            'position'  => 'bottom',
            'direction' => 'horizontal',
            'trigger'   => 'hover',
            'style'     => 'default',
        ] + FontWeightOptions::CARD_DEFAULTS;
    }

    // Front and back share the same field set (the back one "back_"-prefixed,
    // plus the link only the back has). Records saved before an *_align column
    // existed hold '' rather than the TCA default, which would render a broken
    // "text-" class.
    protected function postProcessRows(array $rows, array $settings): array
    {
        $alignFields = ['title_align', 'back_title_align', 'back_link_align'];
        foreach ($rows as &$row) {
            foreach ($alignFields as $field) {
                $row[$field] = $this->resolveChoice($row[$field] ?? null, ['center', 'start', 'end']);
            }
            // Subtitle (and eyebrow) alignment: 'auto' follows the face's title.
            $row['subtitleAlignClass']     = $this->resolveAlignClass($row['subtitle_align'] ?? null, $row['title_align']);
            $row['backSubtitleAlignClass'] = $this->resolveAlignClass($row['back_subtitle_align'] ?? null, $row['back_title_align']);
            $row['faceStyle']     = $this->buildFaceStyle($row, '');
            $row['backFaceStyle'] = $this->buildFaceStyle($row, 'back_');
        }
        unset($row);

        return $rows;
    }

    /**
     * Per-face color overrides as CSS custom properties (see _flip-card.scss);
     * unset or invalid colors are left out so the built-in look applies.
     */
    private function buildFaceStyle(array $row, string $prefix): string
    {
        $style = '';
        foreach (self::COLOR_VARS as $field => $cssVar) {
            $color = $this->sanitizeHexColor((string)($row[$prefix . $field] ?? ''), '');
            if ($color !== '') {
                $style .= $cssVar . ': ' . $color . ';';
            }
        }

        return $style;
    }

    /** First entry of $allowed is the default. */
    private function resolveChoice(?string $value, array $allowed): string
    {
        return in_array($value, $allowed, true) ? $value : $allowed[0];
    }
}
