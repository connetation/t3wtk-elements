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

return [
    'ctrl' => [
        'title'         => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:model.pricing_tier',
        'label'         => 'name',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'versioningWS'        => true,
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_t3wtk_elements/Resources/Public/Icons/pricing-ce.svg',
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
        'name' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.name',
            'config' => [
                'type'     => 'input',
                'size'     => 40,
                'max'      => 100,
                'eval'     => 'trim',
                'required' => true,
            ],
        ],
        'name_layout' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:layout.heading',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingLayoutItems,
                'default'    => 0,
            ],
        ],
        'name_class' => [
            'label'  => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingClassItems,
                'default'    => '',
            ],
        ],
        'tagline' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.tagline',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'max'  => 255,
                'eval' => 'trim',
            ],
        ],
        'tagline_layout' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:layout.subheading',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingLayoutItems,
                'default'    => 0,
            ],
        ],
        'tagline_class' => [
            'label'  => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:subheader_class',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingClassItems,
                'default'    => '',
            ],
        ],
        'currency' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.currency',
            'config' => [
                'type'    => 'input',
                'size'    => 5,
                'max'     => 5,
                'eval'    => 'trim',
                'default' => '€',
            ],
        ],
        'amount' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.amount',
            'config' => [
                'type' => 'input',
                'size' => 15,
                'max'  => 30,
                'eval' => 'trim',
            ],
        ],
        'period' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.period',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'max'  => 50,
                'eval' => 'trim',
            ],
        ],
        'price_note' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.price_note',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'max'  => 255,
                'eval' => 'trim',
            ],
        ],
        'features' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.features',
            'config' => [
                'type' => 'text',
                'rows' => 8,
                'placeholder' => 'One feature per line. Prefix with - to mark as unavailable.',
            ],
        ],
        'cta_label' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.cta_label',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max'  => 100,
                'eval' => 'trim',
            ],
        ],
        'cta_link' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.cta_link',
            'config' => [
                'type'         => 'link',
                'allowedTypes' => ['page', 'url', 'email'],
            ],
        ],
        'featured' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.featured',
            'config' => [
                'type'       => 'check',
                'renderType' => 'checkboxToggle',
                'items'      => [['label' => '']],
            ],
        ],
        'badge_text' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.badge_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max'  => 60,
                'eval' => 'trim',
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    name, name_layout, name_class, tagline, tagline_layout, tagline_class, currency, amount, period, price_note, features,
                --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.tab.cta,
                    cta_label, cta_link,
                --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.tab.highlight,
                    featured, badge_text,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
];
