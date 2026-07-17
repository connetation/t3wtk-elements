<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title'         => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:model.tab_item',
        'label'         => 'title',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'versioningWS'        => true,
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_elements/Resources/Public/Icons/tabs-ce.svg',
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
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:tab_item.title',
            'config' => [
                'type'     => 'input',
                'size'     => 40,
                'max'      => 60,
                'eval'     => 'trim',
                'required' => true,
            ],
        ],
        'body' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:tab_item.body',
            'config' => [
                'type'           => 'text',
                'enableRichtext' => true,
                'rows'           => 8,
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    title, body,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
];
