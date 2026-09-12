<?php

declare(strict_types=1);

defined('TYPO3') or die();

// Adds a "Muted" checkbox next to core's own "Autoplay" checkbox in the
// video/audio overlay palette (shown automatically for any video/audio FAL
// relation, e.g. Video Hero's Media field). Video Hero's Background loop
// mode reads both directly off the media relation instead of hardcoding
// them, so editors control autoplay/mute per media file, not per element.
$GLOBALS['TCA']['sys_file_reference']['columns']['muted'] = [
    'exclude' => true,
    'label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:sys_file_reference.muted',
    'description' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:sys_file_reference.muted.description',
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
