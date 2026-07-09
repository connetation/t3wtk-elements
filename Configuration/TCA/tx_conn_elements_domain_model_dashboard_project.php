<?php

declare(strict_types=1);

defined('TYPO3') or die();

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
        'title'         => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:model.project',
        'label'         => 'title',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'versioningWS'        => true,
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_elements/Resources/Public/Icons/project.svg',
        'security'      => ['ignorePageTypeRestriction' => true],
    ],
    'columns' => [
        'hidden' => [
            'label'  => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => ['type' => 'check', 'renderType' => 'checkboxToggle', 'items' => [['label' => '', 'invertStateDisplay' => true]]],
        ],
        'title' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:project.title',
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
        'description' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:project.description',
            'config' => ['type' => 'text', 'enableRichtext' => true, 'rows' => 5],
        ],
        'status' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:project.status',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'Active',      'value' => 'active'],
                    ['label' => 'In Progress', 'value' => 'in_progress'],
                    ['label' => 'Coming Soon', 'value' => 'coming_soon'],
                    ['label' => 'Deprecated',  'value' => 'deprecated'],
                    ['label' => 'Archived',    'value' => 'archived'],
                    ['label' => 'Inactive',    'value' => 'inactive'],
                ],
                'default' => 'active',
            ],
        ],
        'category' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:project.category',
            'config' => [
                'type'                => 'select',
                'renderType'          => 'selectSingle',
                'foreign_table'       => 'tx_conn_elements_domain_model_dashboard_projectcategory',
                'foreign_table_where' => 'AND {#tx_conn_elements_domain_model_dashboard_projectcategory}.{#hidden}=0 AND {#tx_conn_elements_domain_model_dashboard_projectcategory}.{#deleted}=0',
                'items'               => [['label' => '— none —', 'value' => 0]],
                'default'             => 0,
            ],
        ],
        'url' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:project.url',
            'config' => ['type' => 'link'],
        ],
        'tags' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:project.tags',
            'config' => ['type' => 'input', 'size' => 50, 'placeholder' => 'e.g. PHP, TYPO3, React'],
        ],
        'image' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:project.image',
            'config' => [
                'type'     => 'file',
                'minitems' => 0,
                'maxitems' => 1,
                'allowed'  => 'common-image-types',
            ],
        ],
        'content_element' => [
            'config' => ['type' => 'passthrough'],
        ],
        'sorting' => [
            'config' => ['type' => 'passthrough'],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;General,
                    title,title_layout,status,category,
                --div--;Details,
                    description,url,tags,image,
                --div--;Access,
                    hidden,
            ',
        ],
    ],
];
