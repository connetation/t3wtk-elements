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
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

final class AlertSettingsProcessor implements DataProcessorInterface
{
    private const SETTINGS_FIELD = 'tx_conn_t3wtk_elements_alert_settings';
    private const STYLES = ['info', 'success', 'warning', 'danger', 'custom'];
    private const POSITIONS = ['static', 'fixed-top', 'fixed-bottom', 'edge-left', 'edge-right'];
    private const VERTICAL_ALIGNS = ['top', 'center', 'bottom'];
    private const BUTTON_SHAPES = ['circle', 'square'];
    private const CONTENT_ALIGNS = ['start', 'center', 'end'];
    // Left/right edge positions are always a small button that expands on
    // click (there's no full-width bar variant for them); top/bottom are
    // always the full-width bar
    private const EDGE_POSITIONS = ['edge-left', 'edge-right'];

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $flexData = $flexFormService->convertFlexFormContentToArray(
            (string)($processedData['data'][self::SETTINGS_FIELD] ?? '')
        );
        $raw = $flexData['settings'] ?? [];

        $style = $raw['style'] ?? 'info';
        $position = $raw['position'] ?? 'static';
        $position = in_array($position, self::POSITIONS, true) ? $position : 'static';
        $verticalAlign = $raw['verticalAlign'] ?? 'center';
        $verticalAlign = in_array($verticalAlign, self::VERTICAL_ALIGNS, true) ? $verticalAlign : 'center';
        $buttonShape = $raw['buttonShape'] ?? 'circle';
        $buttonShape = in_array($buttonShape, self::BUTTON_SHAPES, true) ? $buttonShape : 'circle';
        $isEdgeButton = in_array($position, self::EDGE_POSITIONS, true);
        $contentAlign = $raw['contentAlign'] ?? 'start';
        $contentAlign = in_array($contentAlign, self::CONTENT_ALIGNS, true) ? $contentAlign : 'start';
        $borderRadius = RadiusOptions::resolve($raw['borderRadius'] ?? null);

        $settings = [
            'style'               => in_array($style, self::STYLES, true) ? $style : 'info',
            'customAccentColor'   => $this->resolveHexColor($raw['customAccentColor'] ?? ''),
            'customBackgroundColor' => $this->resolveHexColor($raw['customBackgroundColor'] ?? ''),
            'customTextColor'     => $this->resolveHexColor($raw['customTextColor'] ?? ''),
            'icon'                => !empty($raw['icon'] ?? true),
            'dismissible'         => !empty($raw['dismissible']),
            'rememberDismiss'     => !empty($raw['rememberDismiss']),
            'closeColor'          => $this->resolveHexColor($raw['closeColor'] ?? ''),
            'position'            => $position,
            'isFixed'             => $position !== 'static',
            // Only Top/Bottom (a full-width bar) push page content — the
            // edge buttons float over the page like any other floating
            // action button, they don't reserve space for themselves.
            'pushContent'         => $position === 'static' || $isEdgeButton ? false : !empty($raw['pushContent'] ?? true),
            // widget; Top/Bottom are always the full bar. Position alone
            'compact'             => $isEdgeButton,
            'verticalAlign'       => $verticalAlign,
            'buttonShape'         => $buttonShape,
            'contentAlign'        => $contentAlign,
            'borderColor'         => $this->resolveHexColor($raw['borderColor'] ?? ''),
            'borderRadius'        => $borderRadius,
            'buttonColor'         => $this->resolveHexColor($raw['buttonColor'] ?? ''),
            'buttonTextColor'     => $this->resolveHexColor($raw['buttonTextColor'] ?? ''),
        ];

        $typoScriptSettings = GeneralUtility::makeInstance(TypoScriptService::class)
            ->convertTypoScriptArrayToPlainArray($contentObjectConfiguration['settings.'] ?? []);

        $processedData['settings'] = array_replace_recursive($typoScriptSettings, $settings);

        return $processedData; 
    }

    private function resolveHexColor(string $value): ?string
    {
        $value = trim($value);

        return preg_match('/^#[0-9a-fA-F]{6}([0-9a-fA-F]{2})?$/', $value) === 1
            ? $value
            : null;
    }

}
