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
use Connetation\T3wtkBase\Utility\ResponsiveValue;
use Connetation\T3wtkBase\DataProcessing\AbstractItemsProcessor;

final class LightboxGalleryProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_t3wtk_elements_domain_model_gallery_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3wtk_elements_lightbox_gallery_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        // Per device ("desktop,tablet,mobile"); a former single value is desktop only.
        $columns = ResponsiveValue::parse($raw['columns'] ?? null, 1, 6, 3);
        // Small thumbnails (5+ per row) stay a grid on smaller screens too
        // instead of the cards' automatic 2-up / 1-up.
        $isThumbs = $columns['desktop'] >= 5;

        $gapMap = [
            'none'   => 'g-0',
            'small'  => 'g-2',
            'medium' => 'g-3',
            'large'  => 'g-4',
        ];

        $allowedPositions = ['bottom', 'center', 'top'];
        $allowedAligns    = ['left', 'center', 'right'];
        $allowedFits      = ['cover', 'contain'];
        $allowedGaps      = ['none', 'small', 'medium', 'large'];
        $allowedBorders   = ['none', 'solid', 'dashed', 'dotted'];

        $textPosition = in_array($raw['textPosition'] ?? '', $allowedPositions, true)
            ? $raw['textPosition']
            : 'bottom';
        $textAlign = in_array($raw['textAlign'] ?? '', $allowedAligns, true)
            ? $raw['textAlign']
            : 'left';
        $fit = in_array($raw['fit'] ?? '', $allowedFits, true)
            ? $raw['fit']
            : 'cover';
        $gap = in_array($raw['gap'] ?? '', $allowedGaps, true)
            ? $raw['gap']
            : 'medium';
        $borderStyle = in_array($raw['borderStyle'] ?? '', $allowedBorders, true)
            ? $raw['borderStyle']
            : 'dashed';
        $borderRadius = RadiusOptions::resolve($raw['borderRadius'] ?? null);

        // Native color-with-opacity FlexForm fields; empty just means "use
        // the theme default", so this only guards against garbage — no
        // forced fallback hex like AbstractItemsProcessor::sanitizeHexColor.
        $borderColor     = $this->sanitizeOptionalHexColor($raw['borderColor'] ?? '');
        $backgroundColor = $this->sanitizeOptionalHexColor($raw['backgroundColor'] ?? '');

        return [
            'columns'                => $columns['desktop'],
            'colClass'               => '',
            'rowColsClass'           => ResponsiveValue::rowColsClass($columns, $isThumbs ? 4 : null, $isThumbs ? 3 : null),
            'gapClass'               => $gapMap[$gap] ?? $gapMap['medium'],
            'textPosition'           => $textPosition,
            'textAlign'              => $textAlign,
            'fit'                    => $fit,
            'borderStyle'            => $borderStyle,
            'borderColor'            => $borderColor,
            'borderRadius'           => $borderRadius,
            'backgroundColor'        => $backgroundColor,
            'captionInLightboxOnly'  => (bool)($raw['captionInLightboxOnly'] ?? false),
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'columns'                => 3,
            'colClass'               => '',
            'rowColsClass'           => ResponsiveValue::rowColsClass(['desktop' => 3, 'tablet' => null, 'mobile' => null]),
            'gapClass'               => 'g-3',
            'textPosition'           => 'bottom',
            'textAlign'              => 'left',
            'fit'                    => 'cover',
            'borderStyle'            => 'dashed',
            'borderColor'            => '',
            'borderRadius'           => RadiusOptions::THEME_DEFAULT,
            'backgroundColor'        => '',
            'captionInLightboxOnly'  => false,
        ];
    }

    private function sanitizeOptionalHexColor(string $value): string
    {
        $value = trim($value);
        return preg_match('/^#[0-9a-fA-F]{3,8}$/', $value) ? $value : '';
    }
}
