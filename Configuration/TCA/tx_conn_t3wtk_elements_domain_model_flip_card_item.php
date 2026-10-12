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

// Same value set as TYPO3 core's native tt_content.header_layout: 0=default,
// 1-5=H1-H5, 100=hidden. Reused for every heading/subheading layout column.
$headingLayoutItems = [
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_layout.default', 'value' => 0],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_layout.h1', 'value' => 1],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_layout.h2', 'value' => 2],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_layout.h3', 'value' => 3],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_layout.h4', 'value' => 4],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_layout.h5', 'value' => 5],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_layout.hidden', 'value' => 100],
];

// Visual size, independent of the tag *_layout picks — same pattern/labels as
// conn_t3wtk_base's tt_content header_class/subheader_class.
$headingClassItems = [
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.none', 'value' => ''],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.h1', 'value' => 'h1'],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.h2', 'value' => 'h2'],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.h3', 'value' => 'h3'],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.h4', 'value' => 'h4'],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.h5', 'value' => 'h5'],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.hidden', 'value' => 'visually-hidden'],
];

// Bootstrap 5 text-align suffixes ('text-' + this value).
$alignItems = [
    ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:align.left', 'value' => 'start'],
    ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:align.center', 'value' => 'center'],
    ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:align.right', 'value' => 'end'],
];

$select = static fn (string $label, array $items, int|string $default): array => [
    'label'  => $label,
    'config' => [
        'type'       => 'select',
        'renderType' => 'selectSingle',
        'items'      => $items,
        'default'    => $default,
    ],
];

// Front and back offer the same fields — the back ones are the same columns
// with a "back_" prefix — except the link, which only the back has: a click on
// the front flips the card, so a link there would compete with that. Only the
// front title is required (it is the record label and what the card shows
// before it flips).
$faceColumns = static function (string $prefix, bool $titleRequired) use ($headingLayoutItems, $headingClassItems, $alignItems, $select): array {
    $ll = 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:';

    return [
        $prefix . 'title' => [
            'label'  => $ll . 'card_item.title',
            'config' => [
                'type'     => 'input',
                'size'     => 60,
                'max'      => 255,
                'eval'     => 'trim',
                'required' => $titleRequired,
            ],
        ],
        $prefix . 'title_layout'    => $select($ll . 'layout.heading', $headingLayoutItems, 0),
        $prefix . 'title_class'     => $select('LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class', $headingClassItems, ''),
        $prefix . 'title_align'     => $select($ll . 'layout.align', $alignItems, 'center'),
        $prefix . 'subtitle' => [
            'label'  => $ll . 'card_item.subtitle',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'max'  => 255,
                'eval' => 'trim',
            ],
        ],
        $prefix . 'subtitle_layout' => $select($ll . 'layout.subheading', $headingLayoutItems, 0),
        $prefix . 'subtitle_class'  => $select('LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:subheader_class', $headingClassItems, ''),
        // Positions the subtitle — also when shown as eyebrow. Auto = title alignment.
        $prefix . 'subtitle_align'  => $select($ll . 'layout.align', [['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:align.auto', 'value' => 'auto'], ...$alignItems], 'auto'),
        // Same idea as conn_t3wtk_base's tt_content "Subheader as Eyebrow":
        // the subtitle moves above the title as a small uppercase label.
        $prefix . 'subtitle_eyebrow' => [
            'label'  => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:subheader_eyebrow',
            'config' => [
                'type'       => 'check',
                'renderType' => 'checkboxToggle',
                'items'      => [['label' => '']],
                'default'    => 0,
            ],
        ],
        $prefix . 'body' => [
            'label'  => $ll . 'card_item.body',
            'config' => [
                'type'           => 'text',
                'enableRichtext' => true,
                'rows'           => 6,
            ],
        ],
        $prefix . 'image' => [
            'label'       => $ll . 'flip_card_item.image',
            'description' => $ll . 'flip_card_item.image.description',
            'config'      => [
                'type'     => 'file',
                'allowed'  => 'jpg,jpeg,png,gif,webp,svg',
                'maxitems' => 1,
            ],
        ],
        // Empty = built-in look. opacity:true adds an alpha channel (#RRGGBBAA).
        $prefix . 'bg_color' => [
            'label'       => $ll . 'flip_card_item.bg_color',
            'description' => $ll . 'flip_card_item.bg_color.description',
            'config'      => ['type' => 'color', 'opacity' => true],
        ],
        $prefix . 'title_color' => [
            'label'  => $ll . 'flip_card_item.title_color',
            'config' => ['type' => 'color', 'opacity' => true],
        ],
        $prefix . 'subtitle_color' => [
            'label'  => $ll . 'flip_card_item.subtitle_color',
            'config' => ['type' => 'color', 'opacity' => true],
        ],
        $prefix . 'text_color' => [
            'label'       => $ll . 'flip_card_item.text_color',
            'description' => $ll . 'flip_card_item.text_color.description',
            'config'      => ['type' => 'color', 'opacity' => true],
        ],
    ];
};

// Back only — see $faceColumns.
$backLinkColumns = [
    'back_link' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_item.link',
        'config' => [
            'type'         => 'link',
            'allowedTypes' => ['page', 'url', 'email', 'telephone'],
        ],
    ],
    'back_link_label' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_item.link_label',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'max'  => 100,
            'eval' => 'trim',
        ],
    ],
    'back_link_align' => $select('LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:layout.align', $alignItems, 'center'),
];

$facePalettes = static function (string $prefix): array {
    $ll = 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:';

    return [
        $prefix . 'header' => [
            'label'    => $ll . 'card_item.palette.header',
            'showitem' => "{$prefix}title, {$prefix}title_layout, --linebreak--, {$prefix}title_class, {$prefix}title_align",
        ],
        $prefix . 'subheader' => [
            'label'    => $ll . 'card_item.palette.subheader',
            'showitem' => "{$prefix}subtitle, {$prefix}subtitle_layout, --linebreak--, {$prefix}subtitle_class, {$prefix}subtitle_align, --linebreak--, {$prefix}subtitle_eyebrow",
        ],
        $prefix . 'colors' => [
            'label'    => $ll . 'flip_card_item.palette.colors',
            'showitem' => "{$prefix}bg_color, {$prefix}title_color, --linebreak--, {$prefix}subtitle_color, {$prefix}text_color",
        ],
    ];
};

$faceShowitem = static fn (string $prefix): string =>
    "--palette--;;{$prefix}header, --palette--;;{$prefix}subheader, {$prefix}body, {$prefix}image, --palette--;;{$prefix}colors,";

return [
    'ctrl' => [
        'title'         => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:model.flip_card_item',
        'label'         => 'title',
        'label_alt'     => 'back_title',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'versioningWS'  => true,
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_t3wtk_elements/Resources/Public/Icons/flip-card-ce.svg',
        'rootLevel'     => -1,
        'security'      => ['ignorePageTypeRestriction' => true],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config'  => [
                'type'       => 'check',
                'renderType' => 'checkboxToggle',
                'items'      => [['label' => '', 'invertStateDisplay' => true]],
            ],
        ],
        'sorting' => [
            'config' => ['type' => 'passthrough'],
        ],
        'content_element' => [
            'config' => ['type' => 'passthrough'],
        ],
        ...$faceColumns('', true),
        ...$faceColumns('back_', false),
        ...$backLinkColumns,
    ],
    'palettes' => [
        ...$facePalettes(''),
        ...$facePalettes('back_'),
        'back_link' => [
            'label'    => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_item.palette.link',
            'showitem' => 'back_link, back_link_label, back_link_align',
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:flip_card_item.tab.front,
                    ' . $faceShowitem('') . '
                --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:flip_card_item.tab.back,
                    ' . $faceShowitem('back_') . ' --palette--;;back_link,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
];
