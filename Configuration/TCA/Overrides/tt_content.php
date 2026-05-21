<?php

declare(strict_types=1);

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

// ─── Register CE groups ───────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups']['conn'] =
    'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce_group.conn';

$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups']['conn_bootstrap'] =
    'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce_group.conn_bootstrap';

// ─── Dashboard ────────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_dashboard'] = 'conn-ce-dashboard';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.dashboard',
    'value' => 'conn_dashboard',
    'icon'  => 'conn-ce-dashboard',
    'group' => 'conn',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_elements_dashboard_projects' => [
        'label'  => 'Projects',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_elements_domain_model_dashboard_project',
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
            'foreign_table' => 'tx_conn_elements_domain_model_dashboard_projectcategory',
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

$GLOBALS['TCA']['tt_content']['types']['conn_dashboard'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;header,tx_conn_elements_dashboard_projects,
        --div--;LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:dashboard.tab.config,
            tx_conn_elements_dashboard_settings,tx_conn_elements_dashboard_categories,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Bootstrap Card Group ─────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_bs_card_group'] = 'conn-ce-card-group';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_card_group',
    'value' => 'conn_bs_card_group',
    'icon'  => 'conn-ce-card-group',
    'group' => 'conn_bootstrap',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_elements_card_group_items' => [
        'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:card_group.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_elements_domain_model_card_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 100,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'showSynchronizationLink'         => true,
                'showAllLocalizationLink'         => true,
                'showPossibleLocalizationRecords' => true,
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_elements_card_group_settings' => [
        'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:card_group.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => [
                'default' => 'FILE:EXT:conn_elements/Configuration/FlexForms/CardGroup.xml',
            ],
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_bs_card_group'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;header,tx_conn_elements_card_group_items,
        --div--;LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:card_group.tab.config,
            tx_conn_elements_card_group_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Bootstrap Accordion ──────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_bs_accordion'] = 'conn-ce-accordion';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_accordion',
    'value' => 'conn_bs_accordion',
    'icon'  => 'conn-ce-accordion',
    'group' => 'conn_bootstrap',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_elements_accordion_items' => [
        'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:accordion.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_elements_domain_model_accordion_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 200,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'showSynchronizationLink'         => true,
                'showAllLocalizationLink'         => true,
                'showPossibleLocalizationRecords' => true,
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_elements_accordion_settings' => [
        'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:accordion.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => [
                'default' => 'FILE:EXT:conn_elements/Configuration/FlexForms/Accordion.xml',
            ],
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_bs_accordion'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;header,tx_conn_elements_accordion_items,
        --div--;LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:accordion.tab.config,
            tx_conn_elements_accordion_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Swiper group ────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups']['conn_swiper'] =
    'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce_group.conn_swiper';

// ─── Swiper Hero Slider ───────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_swiper_hero_slider'] = 'conn-ce-hero-slider';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_hero_slider',
    'value' => 'conn_swiper_hero_slider',
    'icon'  => 'conn-ce-hero-slider',
    'group' => 'conn_swiper',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_elements_hero_slider_items' => [
        'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:hero_slider.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_elements_domain_model_slide_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 50,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'showSynchronizationLink'         => true,
                'showAllLocalizationLink'         => true,
                'showPossibleLocalizationRecords' => true,
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_elements_hero_slider_settings' => [
        'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:hero_slider.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => [
                'default' => 'FILE:EXT:conn_elements/Configuration/FlexForms/HeroSlider.xml',
            ],
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_swiper_hero_slider'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;header,tx_conn_elements_hero_slider_items,
        --div--;LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:hero_slider.tab.config,
            tx_conn_elements_hero_slider_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Swiper Slider ────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_swiper_slider'] = 'conn-ce-slider';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_slider',
    'value' => 'conn_swiper_slider',
    'icon'  => 'conn-ce-slider',
    'group' => 'conn_swiper',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_elements_slider_items' => [
        'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slider.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_elements_domain_model_slide_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 50,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'showSynchronizationLink'         => true,
                'showAllLocalizationLink'         => true,
                'showPossibleLocalizationRecords' => true,
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_elements_slider_settings' => [
        'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slider.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => [
                'default' => 'FILE:EXT:conn_elements/Configuration/FlexForms/Slider.xml',
            ],
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_swiper_slider'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;header,tx_conn_elements_slider_items,
        --div--;LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slider.tab.config,
            tx_conn_elements_slider_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Wiki ─────────────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_wiki'] = 'conn-ce-wiki';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.wiki',
    'value' => 'conn_wiki',
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

$GLOBALS['TCA']['tt_content']['types']['conn_wiki'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;header,tx_conn_elements_wiki_id,
        --div--;LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:wiki.tab.config,
            tx_conn_elements_wiki_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];
