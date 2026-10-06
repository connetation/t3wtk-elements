<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title'         => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:model.process_step',
        'label'         => 'title',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'versioningWS'        => true,
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_t3wtk_elements/Resources/Public/Icons/process-ce.svg',
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
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:process_step.title',
            'config' => [
                'type'     => 'input',
                'size'     => 40,
                'max'      => 100,
                'eval'     => 'trim',
                'required' => true,
            ],
        ],
        'icon' => [
            'label'       => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:process_step.icon',
            'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:process_step.icon.description',
            'config'      => [
                'type'          => 'select',
                'renderType'    => 'connIconSelect',
                'itemsProcFunc' => \Connetation\T3wtkElements\Service\IconItemsProcFunc::class . '->getIconNameItems',
                'items'         => [
                    ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.name.empty', 'value' => ''],
                ],
            ],
        ],
        'body' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:process_step.body',
            'config' => [
                'type'           => 'text',
                'enableRichtext' => true,
                'rows'           => 6,
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    title, icon, body,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
];
