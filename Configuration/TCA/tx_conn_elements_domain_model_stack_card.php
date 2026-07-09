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

return [
    'ctrl' => [
        'title'         => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:model.stack_card',
        'label'         => 'title',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'versioningWS'        => true,
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_elements/Resources/Public/Icons/card-stack-ce.svg',
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
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:stack_card.title',
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
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:stack_card.subtitle',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'max'  => 100,
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
        'body' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:stack_card.body',
            'config' => [
                'type'           => 'text',
                'enableRichtext' => true,
                'rows'           => 6,
            ],
        ],
        'image' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:stack_card.image',
            'config' => [
                'type'     => 'file',
                'allowed'  => 'jpg,jpeg,png,gif,webp,svg',
                'maxitems' => 1,
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    title, title_layout, subtitle, subtitle_layout, body, image,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
];
