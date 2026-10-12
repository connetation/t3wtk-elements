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
        'title'         => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:model.person',
        'label'         => 'name',
        'label_alt'     => 'role',
        'label_alt_force' => true,
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'versioningWS'  => true,
        'enablecolumns' => [
            'disabled'  => 'hidden',
            'starttime' => 'starttime',
            'endtime'   => 'endtime',
        ],
        'iconfile'  => 'EXT:conn_t3wtk_elements/Resources/Public/Icons/people-ce.svg',
        'security'  => ['ignorePageTypeRestriction' => true],
        'searchFields' => 'name,role,email',
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
        'starttime' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config'  => [
                'type'     => 'datetime',
                'default'  => 0,
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config'  => [
                'type'    => 'datetime',
                'default' => 0,
                'range'   => ['upper' => mktime(0, 0, 0, 1, 1, 2038)],
            ],
        ],
        'sorting' => [
            'config' => ['type' => 'passthrough'],
        ],
        'name' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:person.name',
            'config' => [
                'type'     => 'input',
                'size'     => 60,
                'max'      => 255,
                'eval'     => 'trim',
                'required' => true,
            ],
        ],
        'role' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:person.role',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'max'  => 255,
                'eval' => 'trim',
            ],
        ],
        'image' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:person.image',
            'config' => [
                'type'     => 'file',
                'allowed'  => 'jpg,jpeg,png,gif,webp',
                'maxitems' => 1,
            ],
        ],
        'bio' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:person.bio',
            'config' => [
                'type'           => 'text',
                'enableRichtext' => true,
                'rows'           => 6,
            ],
        ],
        'email' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:person.email',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'max'  => 255,
                'eval' => 'trim',
            ],
        ],
        'phone' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:person.phone',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'max'  => 60,
                'eval' => 'trim',
            ],
        ],
        'link' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:person.link',
            'config' => [
                'type'         => 'link',
                'allowedTypes' => ['page', 'url'],
            ],
        ],
        'link_label' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:person.link_label',
            'config' => [
                'type'        => 'input',
                'size'        => 30,
                'max'         => 100,
                'eval'        => 'trim',
                'placeholder' => 'LinkedIn / Profile',
            ],
        ],
        'accent' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:person.accent',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => [
                    ['label' => 'Auto (alternate)', 'value' => 'auto'],
                    ['label' => 'Accent A', 'value' => 'a'],
                    ['label' => 'Accent B', 'value' => 'b'],
                ],
                'default' => 'auto',
            ],
        ],
        'image_side' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:person.image_side',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => [
                    ['label' => 'Auto (alternate)', 'value' => 'auto'],
                    ['label' => 'Left', 'value' => 'left'],
                    ['label' => 'Right', 'value' => 'right'],
                ],
                'default' => 'auto',
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    name, role, image, bio, accent, image_side,
                --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:person.tab.contact,
                    email, phone, link, link_label,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden, starttime, endtime,
            ',
        ],
    ],
];
