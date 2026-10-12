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

return [
    'ctrl' => [
        'title'         => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:model.logo_item',
        'label'         => 'alt',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'versioningWS'        => true,
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_t3wtk_elements/Resources/Public/Icons/logo-wall-ce.svg',
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
        'image' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:logo_item.image',
            'config' => [
                'type'     => 'file',
                'allowed'  => 'jpg,jpeg,png,gif,webp,svg',
                'maxitems' => 1,
                'required' => true,
            ],
        ],
        'alt' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:logo_item.alt',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'max'  => 255,
                'eval' => 'trim',
            ],
        ],
        'link' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:logo_item.link',
            'config' => [
                'type'         => 'link',
                'allowedTypes' => ['page', 'url'],
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    image, alt, link,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
];
