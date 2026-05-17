<?php

declare(strict_types=1);

defined('TYPO3') or die();

return [
    'ctrl' => [
        'title'         => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:model.project',
        'label'         => 'title',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
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
                'foreign_table'       => 'tx_conn_elements_domain_model_projectcategory',
                'foreign_table_where' => 'AND {#tx_conn_elements_domain_model_projectcategory}.{#hidden}=0 AND {#tx_conn_elements_domain_model_projectcategory}.{#deleted}=0',
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
                    title,status,category,
                --div--;Details,
                    description,url,tags,image,
                --div--;Access,
                    hidden,
            ',
        ],
    ],
];
