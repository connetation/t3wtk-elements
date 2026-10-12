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

defined('TYPO3') or die();

// Adds a "Muted" checkbox next to core's own "Autoplay" checkbox in the
// video/audio overlay palette (shown automatically for any video/audio FAL
// relation, e.g. Video Hero's Media field). Video Hero's Background loop
// mode reads both directly off the media relation instead of hardcoding
// them, so editors control autoplay/mute per media file, not per element.
$GLOBALS['TCA']['sys_file_reference']['columns']['muted'] = [
    'exclude' => true,
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:sys_file_reference.muted',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:sys_file_reference.muted.description',
    'config' => [
        'type' => 'check',
        'renderType' => 'checkboxToggle',
        'default' => 1,
    ],
];

foreach (['videoOverlayPalette', 'audioOverlayPalette'] as $palette) {
    $GLOBALS['TCA']['sys_file_reference']['palettes'][$palette]['showitem'] = str_replace(
        'autoplay',
        'autoplay,muted',
        $GLOBALS['TCA']['sys_file_reference']['palettes'][$palette]['showitem']
    );
}
