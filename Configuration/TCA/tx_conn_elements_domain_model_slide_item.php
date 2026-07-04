<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title'         => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:model.slide_item',
        'label'         => 'title',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'versioningWS'        => true,
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_elements/Resources/Public/Icons/slider-ce.svg',
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
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.title',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'max'  => 255,
                'eval' => 'trim',
            ],
        ],
        'subtitle' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.subtitle',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'max'  => 255,
                'eval' => 'trim',
            ],
        ],
        'body' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.body',
            'config' => [
                'type' => 'text',
                'rows' => 5,
            ],
        ],
        'image' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.image',
            'config' => [
                'type'     => 'file',
                'allowed'  => 'jpg,jpeg,png,gif,webp,svg',
                'maxitems' => 1,
            ],
        ],
        'link' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.link',
            'config' => [
                'type'         => 'link',
                'allowedTypes' => ['page', 'url', 'email', 'telephone'],
            ],
        ],
        'link_label' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.link_label',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max'  => 100,
                'eval' => 'trim',
            ],
        ],
        'text_animation' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_animation',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'default'    => '',
                'items'      => [
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_animation.none',   'value' => ''],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_animation.words',  'value' => 'words'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_animation.lines',  'value' => 'lines'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_animation.chars',  'value' => 'chars'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_animation.reveal', 'value' => 'reveal'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_animation.fuzzy',  'value' => 'fuzzy'],
                ],
            ],
        ],
        'animation_duration' => [
            'label'       => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:animation_duration',
            'description' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:animation_duration.description',
            'config'      => [
                'type'    => 'number',
                'default' => 0,
                'range'   => ['lower' => 0, 'upper' => 5000],
            ],
        ],
        'text_pos_v' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_v',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'default'    => '',
                'items'      => [
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_v.bottom', 'value' => ''],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_v.top',    'value' => 'top'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_v.center', 'value' => 'center'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_v.bottom_explicit', 'value' => 'bottom'],
                ],
            ],
        ],
        'text_pos_h' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_h',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'default'    => '',
                'items'      => [
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_h.left',   'value' => ''],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_h.center', 'value' => 'center'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_h.right',  'value' => 'right'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_h.left_explicit', 'value' => 'left'],
                ],
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    title, subtitle, body, image,
                --div--;LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.tab.link,
                    link, link_label,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
                    text_animation, animation_duration,
                    text_pos_v, text_pos_h,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
];
