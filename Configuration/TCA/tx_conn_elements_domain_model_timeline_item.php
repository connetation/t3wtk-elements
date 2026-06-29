<?php

declare(strict_types=1);

defined('TYPO3') or die();

return [
    'ctrl' => [
        'title'          => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:model.timeline_item',
        'label'          => 'title',
        'sortby'         => 'sorting',
        'tstamp'         => 'tstamp',
        'crdate'         => 'crdate',
        'delete'         => 'deleted',
        'enablecolumns'  => ['disabled' => 'hidden'],
        'iconfile'       => 'EXT:conn_elements/Resources/Public/Icons/conn_logo_small.svg',
        'rootLevel'      => -1,
        'security'       => ['ignorePageTypeRestriction' => true],
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
                'type' => 'input',
                'size' => 30,
                'max'  => 255,
                'placeholder' => 'e.g. 2024, Q1 2025, March 2024',
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
                    date, title, body, image,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
];
