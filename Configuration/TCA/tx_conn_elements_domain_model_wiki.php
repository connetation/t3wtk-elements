<?php

declare(strict_types=1);

defined('TYPO3') or die();

return [
    'ctrl' => [
        'title'         => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:model.wiki',
        'label'         => 'title',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_elements/Resources/Public/Icons/wiki-ce.svg',
        'rootLevel'     => -1,
        'security'      => ['ignorePageTypeRestriction' => true],
    ],
    'columns' => [
        'hidden' => [
            'label'  => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type'       => 'check',
                'renderType' => 'checkboxToggle',
                'items'      => [['label' => '', 'invertStateDisplay' => true]],
            ],
        ],
        'title' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:wiki.title',
            'config' => ['type' => 'input', 'size' => 50, 'required' => true],
        ],
        'description' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:wiki.description',
            'config' => ['type' => 'text', 'rows' => 3, 'enableRichtext' => true],
        ],
        'categories' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:wiki.categories',
            'config' => [
                'type'          => 'inline',
                'foreign_table' => 'tx_conn_elements_domain_model_wikicategory',
                'foreign_field' => 'wiki',
                'maxitems'      => 100,
                'appearance'    => [
                    'collapseAll'        => true,
                    'levelLinksPosition' => 'top',
                    'enabledControls'    => ['dragdrop' => true],
                ],
            ],
        ],
        'sorting' => ['config' => ['type' => 'passthrough']],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;General,
                    title,description,
                --div--;Categories,
                    categories,
                --div--;Access,
                    hidden,
            ',
        ],
    ],
];
