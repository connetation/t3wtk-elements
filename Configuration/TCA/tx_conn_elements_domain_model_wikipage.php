<?php

declare(strict_types=1);

defined('TYPO3') or die();

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
        'title_layout' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:layout.heading',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingLayoutItems,
                'default'    => 0,
            ],
        ],
        'intro' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:wikipage.intro',
            'config' => ['type' => 'text', 'rows' => 3, 'enableRichtext' => true],
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
                'type'       => 'input',
                'size'       => 60,
                'eval'       => '',
                'searchable' => false,
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
                    title,title_layout,intro,
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
