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

// Visual size, independent of the tag *_layout picks — lets an editor keep
// the SEO-correct heading level while styling it like a different size
// (e.g. an <h3> that looks like an .h5). Same pattern/labels as conn_t3wtk_base's
// tt_content header_class/subheader_class. Reused for every *_class column.
$headingClassItems = [
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.none', 'value' => ''],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.h1', 'value' => 'h1'],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.h2', 'value' => 'h2'],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.h3', 'value' => 'h3'],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.h4', 'value' => 'h4'],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.h5', 'value' => 'h5'],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.hidden', 'value' => 'visually-hidden'],
];

// Bootstrap 5 text-align suffixes ('' + this value = 'text-start'/'text-center'/'text-end').
// Shared, reused across every item table's *_align column.
$alignItems = [
    ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:align.left', 'value' => 'start'],
    ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:align.center', 'value' => 'center'],
    ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:align.right', 'value' => 'end'],
];

return [
    'ctrl' => [
        'title'         => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:model.card_item',
        'label'         => 'title',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'versioningWS'        => true,
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_t3wtk_elements/Resources/Public/Icons/card-group-ce.svg',
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
        'title' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_item.title',
            'config' => [
                'type'     => 'input',
                'size'     => 60,
                'max'      => 255,
                'eval'     => 'trim',
                'required' => true,
            ],
        ],
        'title_layout' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:layout.heading',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingLayoutItems,
                'default'    => 0,
            ],
        ],
        'title_class' => [
            'label'  => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingClassItems,
                'default'    => '',
            ],
        ],
        'subtitle' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_item.subtitle',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'max'  => 255,
                'eval' => 'trim',
            ],
        ],
        'subtitle_layout' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:layout.subheading',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingLayoutItems,
                'default'    => 0,
            ],
        ],
        'subtitle_class' => [
            'label'  => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:subheader_class',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingClassItems,
                'default'    => '',
            ],
        ],
        // Same idea as conn_t3wtk_base's tt_content "Subheader as Eyebrow":
        // the subtitle moves above the title as a small uppercase label.
        'subtitle_eyebrow' => [
            'label'  => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:subheader_eyebrow',
            'config' => [
                'type'       => 'check',
                'renderType' => 'checkboxToggle',
                'items'      => [['label' => '']],
                'default'    => 0,
            ],
        ],
        'title_align' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:layout.align',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $alignItems,
                'default'    => 'start',
            ],
        ],
        // Positions the subtitle — also when shown as eyebrow. Auto = title alignment.
        'subtitle_align' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:layout.align',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => [['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:align.auto', 'value' => 'auto'], ...$alignItems],
                'default'    => 'auto',
            ],
        ],
        'badge' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_item.badge',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max'  => 60,
                'eval' => 'trim',
            ],
        ],
        'body' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_item.body',
            'config' => [
                'type'           => 'text',
                'enableRichtext' => true,
                'rows'           => 6,
            ],
        ],
        'image' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_item.image',
            'config' => [
                'type'     => 'file',
                'allowed'  => 'jpg,jpeg,png,gif,webp,svg',
                'maxitems' => 1,
            ],
        ],
        'show_placeholder' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_item.show_placeholder',
            'config' => [
                'type'       => 'check',
                'renderType' => 'checkboxToggle',
                'items'      => [['label' => '']],
                'default'    => 1,
            ],
        ],
        'link' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_item.link',
            'config' => [
                'type'         => 'link',
                'allowedTypes' => ['page', 'url', 'email', 'telephone'],
            ],
        ],
        'link_label' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_item.link_label',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max'  => 100,
                'eval' => 'trim',
            ],
        ],
        'link_align' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:layout.align',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $alignItems,
                'default'    => 'start',
            ],
        ],
    ],
    'palettes' => [
        'header' => [
            'label'    => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_item.palette.header',
            'showitem' => 'title, title_layout, title_class, title_align',
        ],
        'subheader' => [
            'label'    => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_item.palette.subheader',
            'showitem' => 'subtitle, subtitle_layout, subtitle_class, subtitle_align, --linebreak--, subtitle_eyebrow',
        ],
        'link' => [
            'label'    => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_item.palette.link',
            'showitem' => 'link, link_label, link_align',
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;;header, --palette--;;subheader, badge, body, image, show_placeholder,
                --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_item.tab.link,
                    --palette--;;link,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
];
