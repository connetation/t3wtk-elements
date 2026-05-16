<?php

declare(strict_types=1);

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

// ─── Register CE group ────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups']['conn'] =
    'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce_group.conn';

// ─── Dashboard ────────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['dashboard'] = 'conn-ce-dashboard';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.dashboard',
    'value' => 'dashboard',
    'icon'  => 'conn-ce-dashboard',
    'group' => 'conn',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_elements_dashboard_projects' => [
        'label'  => 'Projects',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_elements_domain_model_project',
            'foreign_field' => 'content_element',
            'maxitems'      => 100,
            'appearance'    => [
                'collapseAll'                     => false,
                'levelLinksPosition'              => 'top',
                'showSynchronizationLink'         => true,
                'showAllLocalizationLink'         => true,
                'showPossibleLocalizationRecords' => true,
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_elements_dashboard_categories' => [
        'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:dashboard.categories',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_elements_domain_model_projectcategory',
            'foreign_field' => 'content_element',
            'maxitems'      => 50,
            'appearance'    => [
                'collapseAll'        => false,
                'levelLinksPosition' => 'top',
                'enabledControls'    => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_elements_dashboard_settings' => [
        'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:dashboard.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => [
                'default' => 'FILE:EXT:conn_elements/Configuration/FlexForms/Dashboard.xml',
            ],
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['dashboard'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,header,tx_conn_elements_dashboard_projects,
        --div--;LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:dashboard.tab.config,
            tx_conn_elements_dashboard_settings,tx_conn_elements_dashboard_categories,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Wiki ─────────────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['wiki'] = 'conn-ce-wiki';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.wiki',
    'value' => 'wiki',
    'icon'  => 'conn-ce-wiki',
    'group' => 'conn',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_elements_wiki_id' => [
        'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:wiki.wiki_id',
        'config' => [
            'type'                => 'select',
            'renderType'          => 'selectSingle',
            'foreign_table'       => 'tx_conn_elements_domain_model_wiki',
            'foreign_table_where' => 'AND {#tx_conn_elements_domain_model_wiki}.{#deleted}=0',
            'items'               => [['label' => '— select wiki —', 'value' => 0]],
            'default'             => 0,
        ],
    ],
    'tx_conn_elements_wiki_settings' => [
        'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:wiki.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => [
                'default' => 'FILE:EXT:conn_elements/Configuration/FlexForms/Wiki.xml',
            ],
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['wiki'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,header,tx_conn_elements_wiki_id,
        --div--;LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:wiki.tab.config,
            tx_conn_elements_wiki_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];
