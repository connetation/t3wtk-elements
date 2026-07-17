<?php

declare(strict_types=1);

// Same value set as TYPO3 core's native tt_content.header_layout: 0=default,
// 1-5=H1-H5, 100=hidden. Reused for every heading/subheading layout column.
$headingLayoutItems = [
    ['label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.default_value', 'value' => 0],
    ['label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_layout.I.1', 'value' => 1],
    ['label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_layout.I.2', 'value' => 2],
    ['label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_layout.I.3', 'value' => 3],
    ['label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_layout.I.4', 'value' => 4],
    ['label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_layout.I.5', 'value' => 5],
    ['label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_layout.I.6', 'value' => 100],
];

// Bootstrap 5 text-align suffixes ('' + this value = 'text-start'/'text-center'/'text-end').
// Shared, reused across every item table's *_align column.
$alignItems = [
    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:align.left', 'value' => 'start'],
    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:align.center', 'value' => 'center'],
    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:align.right', 'value' => 'end'],
];

return [
    'ctrl' => [
        'title'         => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:model.card_item',
        'label'         => 'title',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'versioningWS'        => true,
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_elements/Resources/Public/Icons/card-group-ce.svg',
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
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:card_item.title',
            'config' => [
                'type'     => 'input',
                'size'     => 60,
                'max'      => 255,
                'eval'     => 'trim',
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
        'subtitle' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:card_item.subtitle',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'max'  => 255,
                'eval' => 'trim',
            ],
        ],
        'subtitle_layout' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:layout.subheading',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingLayoutItems,
                'default'    => 0,
            ],
        ],
        'title_align' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:layout.align',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $alignItems,
                'default'    => 'start',
            ],
        ],
        'subtitle_align' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:layout.align',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $alignItems,
                'default'    => 'start',
            ],
        ],
        'badge' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:card_item.badge',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max'  => 60,
                'eval' => 'trim',
            ],
        ],
        'body' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:card_item.body',
            'config' => [
                'type'           => 'text',
                'enableRichtext' => true,
                'rows'           => 6,
            ],
        ],
        'image' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:card_item.image',
            'config' => [
                'type'     => 'file',
                'allowed'  => 'jpg,jpeg,png,gif,webp,svg',
                'maxitems' => 1,
            ],
        ],
        'link' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:card_item.link',
            'config' => [
                'type'         => 'link',
                'allowedTypes' => ['page', 'url', 'email', 'telephone'],
            ],
        ],
        'link_label' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:card_item.link_label',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max'  => 100,
                'eval' => 'trim',
            ],
        ],
        'link_align' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:layout.align',
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
            'label'    => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:card_item.palette.header',
            'showitem' => 'title, title_layout, title_align',
        ],
        'subheader' => [
            'label'    => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:card_item.palette.subheader',
            'showitem' => 'subtitle, subtitle_layout, subtitle_align',
        ],
        'link' => [
            'label'    => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:card_item.palette.link',
            'showitem' => 'link, link_label, link_align',
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;;header, --palette--;;subheader, badge, body, image,
                --div--;LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:card_item.tab.link,
                    --palette--;;link,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
];
