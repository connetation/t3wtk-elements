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
        'title'         => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:model.testimonial_item',
        'label'         => 'author_name',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'versioningWS'        => true,
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_elements/Resources/Public/Icons/testimonials-ce.svg',
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
        'quote' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:testimonial_item.quote',
            'config' => [
                'type'           => 'text',
                'enableRichtext' => true,
                'rows'           => 5,
                'required'       => true,
            ],
        ],
        'author_name' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:testimonial_item.author_name',
            'config' => [
                'type'     => 'input',
                'size'     => 60,
                'max'      => 255,
                'eval'     => 'trim',
                'required' => true,
            ],
        ],
        'author_name_layout' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:layout.heading',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingLayoutItems,
                'default'    => 0,
            ],
        ],
        'author_role' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:testimonial_item.author_role',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'max'  => 255,
                'eval' => 'trim',
            ],
        ],
        'author_role_layout' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:layout.subheading',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingLayoutItems,
                'default'    => 0,
            ],
        ],
        'rating' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:testimonial_item.rating',
            'config' => [
                'type'    => 'select',
                'renderType' => 'selectSingle',
                'items'   => [
                    ['label' => '—', 'value' => '0'],
                    ['label' => '★', 'value' => '1'],
                    ['label' => '★★', 'value' => '2'],
                    ['label' => '★★★', 'value' => '3'],
                    ['label' => '★★★★', 'value' => '4'],
                    ['label' => '★★★★★', 'value' => '5'],
                ],
                'default' => '5',
            ],
        ],
        'image' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:testimonial_item.image',
            'config' => [
                'type'     => 'file',
                'allowed'  => 'jpg,jpeg,png,gif,webp',
                'maxitems' => 1,
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    quote, rating,
                --div--;LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:testimonial_item.tab.author,
                    author_name, author_name_layout, author_role, author_role_layout, image,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
];
