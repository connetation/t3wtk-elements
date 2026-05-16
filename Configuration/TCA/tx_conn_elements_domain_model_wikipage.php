<?php

declare(strict_types=1);

defined('TYPO3') or die();

return [
    'ctrl' => [
        'title'         => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:model.wikipage',
        'label'         => 'title',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_elements/Resources/Public/Icons/wiki-page.svg',
        'searchFields'  => 'title,intro',
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
        'wiki' => [
            'config' => ['type' => 'passthrough'],
        ],
        'title' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:wikipage.title',
            'config' => ['type' => 'input', 'size' => 50, 'required' => true],
        ],
        'intro' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:wikipage.intro',
            'config' => ['type' => 'text', 'rows' => 3],
        ],
        'category' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:wikipage.category',
            'config' => [
                'type'                => 'select',
                'renderType'          => 'selectSingle',
                'foreign_table'       => 'tx_conn_elements_domain_model_wikicategory',
                'foreign_table_where' => 'AND {#tx_conn_elements_domain_model_wikicategory}.{#deleted}=0 AND {#tx_conn_elements_domain_model_wikicategory}.{#hidden}=0',
                'items'               => [['label' => '— none —', 'value' => 0]],
                'default'             => 0,
            ],
        ],
        'tags' => [
            'label'       => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:wikipage.tags',
            'description' => 'Comma-separated, e.g. PHP, TYPO3, Performance',
            'config'      => [
                'type' => 'input',
                'size' => 60,
                'eval' => '',
            ],
        ],
        'blocks' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:wikipage.blocks',
            'config' => [
                'type'          => 'inline',
                'foreign_table' => 'tx_conn_elements_domain_model_wikipage_block',
                'foreign_field' => 'page',
                'maxitems'      => 50,
                'appearance'    => [
                    'collapseAll'        => false,
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
                    title,intro,
                --div--;Content,
                    blocks,
                --div--;Meta,
                    category,tags,
                --div--;Access,
                    hidden,
            ',
        ],
    ],
];
