<?php

declare(strict_types=1);

// Same value set as TYPO3 core's native tt_content.header_layout: 0=default,
// 1-5=H1-H5, 100=hidden. Reused for every heading/subheading layout column.
$headingLayoutItems = [
    ['label' => 'LLL:EXT:conn_base/Resources/Private/Language/locallang_be.xlf:header_layout.default', 'value' => 0],
    ['label' => 'LLL:EXT:conn_base/Resources/Private/Language/locallang_be.xlf:header_layout.h1', 'value' => 1],
    ['label' => 'LLL:EXT:conn_base/Resources/Private/Language/locallang_be.xlf:header_layout.h2', 'value' => 2],
    ['label' => 'LLL:EXT:conn_base/Resources/Private/Language/locallang_be.xlf:header_layout.h3', 'value' => 3],
    ['label' => 'LLL:EXT:conn_base/Resources/Private/Language/locallang_be.xlf:header_layout.h4', 'value' => 4],
    ['label' => 'LLL:EXT:conn_base/Resources/Private/Language/locallang_be.xlf:header_layout.h5', 'value' => 5],
    ['label' => 'LLL:EXT:conn_base/Resources/Private/Language/locallang_be.xlf:header_layout.hidden', 'value' => 100],
];

defined('TYPO3') or die();

return [
    'ctrl' => [
        'title'          => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:model.timeline_item',
        'label'          => 'title',
        'sortby'         => 'sorting',
        'tstamp'         => 'tstamp',
        'crdate'         => 'crdate',
        'delete'         => 'deleted',
        'versioningWS'         => true,
        'enablecolumns'  => ['disabled' => 'hidden'],
        'iconfile'       => 'EXT:conn_elements/Resources/Public/Icons/conn_logo_small.svg',
        'rootLevel'      => -1,
        'security'       => ['ignorePageTypeRestriction' => true],
    ],

    'palettes' => [
        'date' => [
            'showitem' => 'date, date_precision',
        ],
    ],

    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.disable',
            'config'  => ['type' => 'check', 'renderType' => 'checkboxToggle'],
        ],
        'sorting' => [
            'config' => ['type' => 'passthrough'],
        ],
        'content_element' => [
            'config' => ['type' => 'passthrough'],
        ],
        'date' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:timeline_item.date',
            'config' => [
                'type'     => 'datetime',
                'format'   => 'date',
                'nullable' => true,
            ],
        ],
        'date_precision' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:timeline_item.date_precision',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => [
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:timeline_item.date_precision.full', 'value' => 'full'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:timeline_item.date_precision.year', 'value' => 'year'],
                ],
                'default' => 'full',
            ],
        ],
        'title' => [
            'label'   => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:timeline_item.title',
            'config'  => [
                'type'     => 'input',
                'size'     => 50,
                'max'      => 255,
                'required' => true,
            ],
        ],
        'title_layout' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:layout.heading',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingLayoutItems,
                'default'    => 0,
            ],
        ],
        'body' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:timeline_item.body',
            'config' => [
                'type'           => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'default',
                'rows'           => 6,
            ],
        ],
        'image' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:timeline_item.image',
            'config' => [
                'type'     => 'file',
                'allowed'  => 'common-image-types',
                'maxitems' => 1,
            ],
        ],
    ],

    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;;date, title, title_layout, body, image,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
];
