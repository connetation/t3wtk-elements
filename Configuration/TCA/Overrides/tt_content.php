<?php

declare(strict_types=1);

/*
 * This file is part of the Connetation TYPO3 Web Toolkit - T3WTK.
 *
 * Copyright (c) Connetation Web Engineering GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('TYPO3') or die();

use B13\Container\Tca\ContainerConfiguration;
use B13\Container\Tca\Registry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

// ─── Text animation – global field for ALL content element types ──
ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_text_animation' => [
        'label'       => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_animation',
        'displayCond' => [
            'AND' => [
                'FIELD:CType:!=:conn_swiper_hero_slider',
                'FIELD:CType:!=:conn_swiper_slider',
                'FIELD:CType:!=:conn_swiper_thumbs_slider',
                'FIELD:CType:!=:conn_swiper_parallax_slider',
            ],
        ],
        'config' => [
            'type'       => 'select',
            'renderType' => 'selectSingle',
            'default'    => '',
            'items'      => [
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_animation.none',  'value' => ''],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_animation.words', 'value' => 'words'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_animation.lines', 'value' => 'lines'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_animation.chars',      'value' => 'chars'],
['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_animation.reveal',     'value' => 'reveal'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_animation.fuzzy',      'value' => 'fuzzy'],
            ],
        ],
    ],
]);

// Inject into the 'frames' palette that every CE type uses in its Appearance tab
ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'frames',
    'tx_conn_t3wtk_elements_text_animation',
    'after:frame_class'
);

// ─── Register CE groups ───────────────────────────────────────────
// Grouped by editor-facing function 
$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups']['conn_sliders'] =
    'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce_group.conn_sliders';

$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups']['conn_galleries'] =
    'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce_group.conn_galleries';

$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups']['conn_interactive'] =
    'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce_group.conn_interactive';

// conn_layout: the 5 Bootstrap Grid containers only — kept standalone
$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups']['conn_layout'] =
    'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce_group.conn_layout';

$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups']['conn_data'] =
    'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce_group.conn_data';

// ─── Bootstrap Card Group ─────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_bs_card_group'] = 'conn-ce-card-group';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_card_group',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_card_group.description',
    'value' => 'conn_bs_card_group',
    'icon'  => 'conn-ce-card-group',
    'group' => 'default',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_card_group_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_group.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_card_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 100,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_card_group_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_group.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/CardGroup.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_bs_card_group'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_card_group_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_group.tab.config,
            tx_conn_t3wtk_elements_card_group_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Subpage Cards ────────────────────────────────────────────────
// Card grid built from page records: title = page title, teaser/link/media
// from the page's "Card" tab (Configuration/TCA/Overrides/pages.php).
// Page selection reuses the core `pages` field, like fsc's menu elements.
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_subpage_cards'] = 'conn-ce-subpage-cards';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.subpage_cards',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.subpage_cards.description',
    'value' => 'conn_subpage_cards',
    'icon'  => 'conn-ce-subpage-cards',
    'group' => 'menu',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_subpage_cards_source' => [
        'label'    => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:subpage_cards.source',
        'onChange' => 'reload',
        'config'   => [
            'type'       => 'select',
            'renderType' => 'selectSingle',
            'default'    => '',
            'items'      => [
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:subpage_cards.source.current',  'value' => ''],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:subpage_cards.source.subpages', 'value' => 'subpages'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:subpage_cards.source.selected', 'value' => 'selected'],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_subpage_cards_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:subpage_cards.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/SubpageCards.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_subpage_cards'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,
            tx_conn_t3wtk_elements_subpage_cards_source,pages,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:subpage_cards.tab.config,
            tx_conn_t3wtk_elements_subpage_cards_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
    'columnsOverrides' => [
        'pages' => [
            'label'       => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:subpage_cards.pages',
            'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:subpage_cards.pages.description',
            'displayCond' => 'FIELD:tx_conn_t3wtk_elements_subpage_cards_source:!=:',
        ],
    ],
];

// ─── Bootstrap Accordion ──────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_bs_accordion'] = 'conn-ce-accordion';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_accordion',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_accordion.description',
    'value' => 'conn_bs_accordion',
    'icon'  => 'conn-ce-accordion',
    'group' => 'conn_interactive',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_accordion_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:accordion.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_accordion_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 200,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_accordion_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:accordion.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/Accordion.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_bs_accordion'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_accordion_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:accordion.tab.config,
            tx_conn_t3wtk_elements_accordion_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Bootstrap Tabs ───────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_bs_tabs'] = 'conn-ce-tabs';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_tabs',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_tabs.description',
    'value' => 'conn_bs_tabs',
    'icon'  => 'conn-ce-tabs',
    'group' => 'conn_interactive',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_tabs_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:tabs.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_tab_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 200,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_tabs_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:tabs.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/Tabs.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_bs_tabs'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_tabs_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:tabs.tab.config,
            tx_conn_t3wtk_elements_tabs_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Process/Steps ──────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_process'] = 'conn-ce-process';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_process',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_process.description',
    'value' => 'conn_process',
    'icon'  => 'conn-ce-process',
    'group' => 'conn_data',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_process_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:process.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_process_step',
            'foreign_field' => 'content_element',
            'maxitems'      => 200,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_process_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:process.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/Process.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_process'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_process_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:process.tab.config,
            tx_conn_t3wtk_elements_process_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Before/After Image Slider ─────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_before_after'] = 'conn-ce-before-after';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_before_after',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_before_after.description',
    'value' => 'conn_before_after',
    'icon'  => 'conn-ce-before-after',
    'group' => 'conn_sliders',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_before_after_before' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.before_image',
        'config' => [
            'type'     => 'file',
            'allowed'  => 'jpg,jpeg,png,gif,webp',
            'maxitems' => 1,
        ],
    ],
    'tx_conn_t3wtk_elements_before_after_before_label' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.before_label',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'max'  => 100,
            'eval' => 'trim',
        ],
    ],
    'tx_conn_t3wtk_elements_before_after_after' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.after_image',
        'config' => [
            'type'     => 'file',
            'allowed'  => 'jpg,jpeg,png,gif,webp',
            'maxitems' => 1,
        ],
    ],
    'tx_conn_t3wtk_elements_before_after_after_label' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.after_label',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'max'  => 100,
            'eval' => 'trim',
        ],
    ],
    'tx_conn_t3wtk_elements_before_after_start' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.start',
        'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.start.description',
        'config' => [
            'type'    => 'number',
            'default' => 50,
            'range'   => ['lower' => 0, 'upper' => 100],
        ],
    ],
    'tx_conn_t3wtk_elements_before_after_direction' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.direction',
        'config' => [
            'type'       => 'select',
            'renderType' => 'selectSingle',
            'default'    => 'left-right',
            'items' => [
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.direction.left_right', 'value' => 'left-right'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.direction.top_bottom', 'value' => 'top-bottom'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.direction.diagonal_tlbr', 'value' => 'diagonal-tlbr'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.direction.diagonal_bltr', 'value' => 'diagonal-bltr'],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_before_after_handle_icon' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.handle_icon',
        'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.handle_icon.description',
        'config' => [
            'type'     => 'file',
            'allowed'  => 'svg,png,webp',
            'maxitems' => 1,
        ],
    ],
    'tx_conn_t3wtk_elements_before_after_handle_color' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.handle_color',
        'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.handle_color.description',
        'config' => [
            'type'    => 'color',
            'opacity' => true,
        ],
    ],
    'tx_conn_t3wtk_elements_before_after_handle_border_color' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.handle_border_color',
        'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.handle_border_color.description',
        'config' => [
            'type'    => 'color',
            'opacity' => true,
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['palettes']['conn_before_after_before'] = [
    'showitem' => 'tx_conn_t3wtk_elements_before_after_before, tx_conn_t3wtk_elements_before_after_before_label',
];
$GLOBALS['TCA']['tt_content']['palettes']['conn_before_after_after'] = [
    'showitem' => 'tx_conn_t3wtk_elements_before_after_after, tx_conn_t3wtk_elements_before_after_after_label',
];
$GLOBALS['TCA']['tt_content']['palettes']['conn_before_after_settings'] = [
    'showitem' => 'tx_conn_t3wtk_elements_before_after_start, tx_conn_t3wtk_elements_before_after_direction, tx_conn_t3wtk_elements_before_after_handle_icon,
        tx_conn_t3wtk_elements_before_after_handle_color, tx_conn_t3wtk_elements_before_after_handle_border_color',
];

$GLOBALS['TCA']['tt_content']['types']['conn_before_after'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:before_after.tab.config,
            --palette--;;conn_before_after_before,
            --palette--;;conn_before_after_after,
            --palette--;;conn_before_after_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Quote ──────────────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_quote'] = 'conn-ce-quote';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.quote',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.quote.description',
    'value' => 'conn_quote',
    'icon'  => 'conn-ce-quote',
    'group' => 'default',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_quote_author' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:quote.author',
        'config' => [
            'type' => 'input',
            'size' => 40,
            'max'  => 255,
            'eval' => 'trim',
        ],
    ],
    'tx_conn_t3wtk_elements_quote_role' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:quote.role',
        'config' => [
            'type' => 'input',
            'size' => 40,
            'max'  => 255,
            'eval' => 'trim',
        ],
    ],
    'tx_conn_t3wtk_elements_quote_image' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:quote.image',
        'config' => [
            'type'     => 'file',
            'allowed'  => 'jpg,jpeg,png,gif,webp',
            'maxitems' => 1,
        ],
    ],
    'tx_conn_t3wtk_elements_quote_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:quote.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/Quote.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['palettes']['conn_quote_author'] = [
    'showitem' => 'tx_conn_t3wtk_elements_quote_author, tx_conn_t3wtk_elements_quote_role, --linebreak--, tx_conn_t3wtk_elements_quote_image',
];

$GLOBALS['TCA']['tt_content']['types']['conn_quote'] = [
    'columnsOverrides' => [
        'bodytext' => [
            'config' => [
                'enableRichtext' => true,
            ],
        ],
    ],
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,
            bodytext,
            --palette--;;conn_quote_author,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:quote.tab.config,
            tx_conn_t3wtk_elements_quote_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Alert/Notice Banner ────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_alert'] = 'conn-ce-alert';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.alert',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.alert.description',
    'value' => 'conn_alert',
    'icon'  => 'conn-ce-alert',
    'group' => 'special',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_alert_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:alert.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/Alert.xml',
        ],
    ],
    'tx_conn_t3wtk_elements_alert_custom_icon_name' => [
        'label'       => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:alert.customIconName',
        'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:alert.customIconName.description',
        'config'      => [
            'type'          => 'select',
            'renderType'    => 'connIconSelect',
            'itemsProcFunc' => \Connetation\T3wtkElements\Service\IconItemsProcFunc::class . '->getIconNameItems',
            'items'         => [
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.name.empty', 'value' => ''],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_alert_custom_icon_image' => [
        'label'       => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:alert.customIconImage',
        'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:alert.customIconImage.description',
        'config'      => [
            'type'     => 'file',
            'allowed'  => 'jpg,jpeg,png,gif,webp,svg',
            'maxitems' => 1,
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['palettes']['conn_alert_custom_icon'] = [
    'showitem' => 'tx_conn_t3wtk_elements_alert_custom_icon_name, --linebreak--, tx_conn_t3wtk_elements_alert_custom_icon_image',
];

$GLOBALS['TCA']['tt_content']['types']['conn_alert'] = [
    'columnsOverrides' => [
        'bodytext' => [
            'config' => [
                'enableRichtext' => true,
            ],
        ],
    ],
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,bodytext,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:alert.tab.config,
            tx_conn_t3wtk_elements_alert_settings,
            --palette--;;conn_alert_custom_icon,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Bootstrap Carousel ───────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_bs_carousel'] = 'conn-ce-bs-carousel';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_carousel',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_carousel.description',
    'value' => 'conn_bs_carousel',
    'icon'  => 'conn-ce-bs-carousel',
    'group' => 'conn_sliders',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_carousel_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:carousel.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_carousel_slide',
            'foreign_field' => 'content_element',
            'maxitems'      => 100,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_carousel_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:carousel.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/Carousel.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_bs_carousel'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_carousel_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:carousel.tab.config,
            tx_conn_t3wtk_elements_carousel_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Swiper Hero Slider ───────────────────────────────────────────
// Hero + Thumbs sliders always render the subtitle as an eyebrow, so the
// shared slide_item "Subheader as Eyebrow" toggle (and its position) is
// dropped from the child form. Done via the child's showitem: overriding the
// columns to type=passthrough is not enough, because overrideChildTca merges
// recursively and the inherited renderType=checkboxToggle still renders.
$slideItemOverrideWithoutEyebrow = [
    'types' => [
        '1' => [
            'showitem' => preg_replace(
                '/\\s*\\bsubtitle_eyebrow(?:_position)?\\s*,/',
                '',
                $GLOBALS['TCA']['tx_conn_t3wtk_elements_domain_model_slide_item']['types']['1']['showitem']
            ),
        ],
    ],
];

$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_swiper_hero_slider'] = 'conn-ce-hero-slider';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_hero_slider',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_hero_slider.description',
    'value' => 'conn_swiper_hero_slider',
    'icon'  => 'conn-ce-hero-slider',
    'group' => 'conn_sliders',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_hero_slider_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:hero_slider.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_slide_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 50,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
            // This slider always renders the subtitle as its eyebrow, so the
            // shared slide_item "Subheader as Eyebrow" toggle has no effect here.
            'overrideChildTca' => $slideItemOverrideWithoutEyebrow,
        ],
    ],
    'tx_conn_t3wtk_elements_hero_slider_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:hero_slider.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/HeroSlider.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_swiper_hero_slider'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_hero_slider_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:hero_slider.tab.config,
            tx_conn_t3wtk_elements_hero_slider_settings,
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
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_slider',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_slider.description',
    'value' => 'conn_swiper_slider',
    'icon'  => 'conn-ce-slider',
    'group' => 'conn_sliders',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_slider_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:slider.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_slide_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 50,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_slider_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:slider.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/Slider.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_swiper_slider'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_slider_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:slider.tab.config,
            tx_conn_t3wtk_elements_slider_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Swiper Thumbnails Slider ─────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_swiper_thumbs_slider'] = 'conn-ce-thumbs-slider';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_thumbs_slider',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_thumbs_slider.description',
    'value' => 'conn_swiper_thumbs_slider',
    'icon'  => 'conn-ce-thumbs-slider',
    'group' => 'conn_sliders',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_thumbs_slider_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:thumbs_slider.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_slide_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 50,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
            // This slider always renders the subtitle as its eyebrow, so the
            // shared slide_item "Subheader as Eyebrow" toggle has no effect here.
            'overrideChildTca' => $slideItemOverrideWithoutEyebrow,
        ],
    ],
    'tx_conn_t3wtk_elements_thumbs_slider_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:thumbs_slider.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/ThumbsSlider.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_swiper_thumbs_slider'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_thumbs_slider_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:thumbs_slider.tab.config,
            tx_conn_t3wtk_elements_thumbs_slider_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Swiper Parallax Slider ───────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_swiper_parallax_slider'] = 'conn-ce-parallax-slider';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_parallax_slider',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_parallax_slider.description',
    'value' => 'conn_swiper_parallax_slider',
    'icon'  => 'conn-ce-parallax-slider',
    'group' => 'conn_sliders',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_parallax_slider_bg_image' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:parallax_slider.bg_image',
        'config' => [
            'type'     => 'file',
            'allowed'  => 'jpg,jpeg,png,gif,webp',
            'maxitems' => 1,
        ],
    ],
    'tx_conn_t3wtk_elements_parallax_slider_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:parallax_slider.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_slide_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 50,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
            'overrideChildTca' => [
                'types' => [
                    '1' => [
                        'showitem' => '
                            title, subtitle, body,
                            --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:slide_item.tab.link,
                                link, link_label,
                            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
                                text_animation, animation_duration,
                                text_pos_v, text_pos_h,
                        ',
                    ],
                ],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_parallax_slider_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:parallax_slider.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/ParallaxSlider.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_swiper_parallax_slider'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,
            tx_conn_t3wtk_elements_parallax_slider_bg_image,
            tx_conn_t3wtk_elements_parallax_slider_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:parallax_slider.tab.config,
            tx_conn_t3wtk_elements_parallax_slider_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Text Column ──────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_text_column'] = 'content-text-columns';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.text_column',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.text_column.description',
    'value' => 'conn_text_column',
    'icon'  => 'content-text-columns',
    'group' => 'default',
]);

$GLOBALS['TCA']['tt_content']['types']['conn_text_column'] = [
    'columnsOverrides' => [
        'bodytext' => [
            'config' => [
                'enableRichtext' => true,
            ],
        ],
    ],
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,bodytext,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── GSAP Card Stack ──────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_card_stack'] = 'conn-ce-card-stack';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.card_stack',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.card_stack.description',
    'value' => 'conn_card_stack',
    'icon'  => 'conn-ce-card-stack',
    'group' => 'conn_sliders',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_card_stack_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_stack.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_stack_card',
            'foreign_field' => 'content_element',
            'maxitems'      => 50,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_card_stack_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_stack.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/CardStack.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_card_stack'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_card_stack_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:card_stack.tab.config,
            tx_conn_t3wtk_elements_card_stack_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── GSAP Shift Carousel ───────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_shift_carousel'] = 'conn-ce-shift-carousel';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.shift_carousel',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.shift_carousel.description',
    'value' => 'conn_shift_carousel',
    'icon'  => 'conn-ce-shift-carousel',
    'group' => 'conn_galleries',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_shift_carousel_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:shift_carousel.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_shift_carousel_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 100,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_shift_carousel_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:shift_carousel.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/ShiftCarousel.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_shift_carousel'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_shift_carousel_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:shift_carousel.tab.config,
            tx_conn_t3wtk_elements_shift_carousel_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── GSAP Lightbox Gallery ────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_lightbox_gallery'] = 'conn-ce-lightbox-gallery';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.lightbox_gallery',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.lightbox_gallery.description',
    'value' => 'conn_lightbox_gallery',
    'icon'  => 'conn-ce-lightbox-gallery',
    'group' => 'conn_galleries',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_lightbox_gallery_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:lightbox_gallery.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_gallery_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 200,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_lightbox_gallery_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:lightbox_gallery.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/LightboxGallery.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_lightbox_gallery'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_lightbox_gallery_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:lightbox_gallery.tab.config,
            tx_conn_t3wtk_elements_lightbox_gallery_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Mosaic Gallery ──────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_mosaic_gallery'] = 'conn-ce-mosaic-gallery';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.mosaic_gallery',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.mosaic_gallery.description',
    'value' => 'conn_mosaic_gallery',
    'icon'  => 'conn-ce-mosaic-gallery',
    'group' => 'conn_galleries',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_mosaic_gallery_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:mosaic_gallery.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_mosaic_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 500,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_mosaic_gallery_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:mosaic_gallery.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/MosaicGallery.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_mosaic_gallery'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_mosaic_gallery_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:mosaic_gallery.tab.config,
            tx_conn_t3wtk_elements_mosaic_gallery_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Stats / Counter ─────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_stats'] = 'conn-ce-stats';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_stats',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_stats.description',
    'value' => 'conn_stats',
    'icon'  => 'conn-ce-stats',
    'group' => 'conn_data',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_stats_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:stats.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_stat_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 50,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_stats_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:stats.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/Stats.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_stats'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_stats_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:stats.tab.config,
            tx_conn_t3wtk_elements_stats_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Logo Wall ───────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_logo_wall'] = 'conn-ce-logo-wall';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_logo_wall',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_logo_wall.description',
    'value' => 'conn_logo_wall',
    'icon'  => 'conn-ce-logo-wall',
    'group' => 'conn_galleries',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_logo_wall_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:logo_wall.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_logo_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 100,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_logo_wall_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:logo_wall.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/LogoWall.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_logo_wall'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_logo_wall_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:logo_wall.tab.config,
            tx_conn_t3wtk_elements_logo_wall_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Testimonials ─────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_testimonials'] = 'conn-ce-testimonials';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_testimonials',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_testimonials.description',
    'value' => 'conn_testimonials',
    'icon'  => 'conn-ce-testimonials',
    'group' => 'conn_data',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_testimonials_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:testimonials.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_testimonial_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 100,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_testimonials_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:testimonials.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/Testimonials.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_testimonials'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_testimonials_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:testimonials.tab.config,
            tx_conn_t3wtk_elements_testimonials_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Pricing Table ────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_bs_pricing'] = 'conn-ce-pricing';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_pricing',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_pricing.description',
    'value' => 'conn_bs_pricing',
    'icon'  => 'conn-ce-pricing',
    'group' => 'conn_data',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_pricing_tiers' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pricing.tiers',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_pricing_tier',
            'foreign_field' => 'content_element',
            'maxitems'      => 10,
            'appearance'    => [
                'collapseAll'                     => false,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_pricing_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pricing.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/Pricing.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_bs_pricing'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_pricing_tiers,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pricing.tab.config,
            tx_conn_t3wtk_elements_pricing_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Feature Grid ─────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_feature_grid'] = 'conn-ce-feature-grid';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_feature_grid',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_feature_grid.description',
    'value' => 'conn_feature_grid',
    'icon'  => 'conn-ce-feature-grid',
    'group' => 'conn_data',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_feature_grid_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:feature_grid.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_feature_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 50,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_feature_grid_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:feature_grid.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/FeatureGrid.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_feature_grid'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_feature_grid_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:feature_grid.tab.config,
            tx_conn_t3wtk_elements_feature_grid_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Bootstrap Grid containers (b13/container) ───────────────────
$registry = GeneralUtility::makeInstance(Registry::class);

$registry->configureContainer(
    (new ContainerConfiguration(
        'conn_grid_2col',
        'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_2col',
        'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_2col.description',
        [
            [
                ['name' => 'Column 1', 'colPos' => 200, 'colspan' => 6],
                ['name' => 'Column 2', 'colPos' => 201, 'colspan' => 6],
            ],
        ]
    ))
    ->setIcon('EXT:conn_t3wtk_elements/Resources/Public/Icons/grid-2col-ce.svg')
    ->setGroup('conn_layout')
);

$registry->configureContainer(
    (new ContainerConfiguration(
        'conn_grid_3col',
        'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_3col',
        'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_3col.description',
        [
            [
                ['name' => 'Column 1', 'colPos' => 210, 'colspan' => 4],
                ['name' => 'Column 2', 'colPos' => 211, 'colspan' => 4],
                ['name' => 'Column 3', 'colPos' => 212, 'colspan' => 4],
            ],
        ]
    ))
    ->setIcon('EXT:conn_t3wtk_elements/Resources/Public/Icons/grid-3col-ce.svg')
    ->setGroup('conn_layout')
);

$registry->configureContainer(
    (new ContainerConfiguration(
        'conn_grid_4col',
        'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_4col',
        'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_4col.description',
        [
            [
                ['name' => 'Column 1', 'colPos' => 220, 'colspan' => 3],
                ['name' => 'Column 2', 'colPos' => 221, 'colspan' => 3],
                ['name' => 'Column 3', 'colPos' => 222, 'colspan' => 3],
                ['name' => 'Column 4', 'colPos' => 223, 'colspan' => 3],
            ],
        ]
    ))
    ->setIcon('EXT:conn_t3wtk_elements/Resources/Public/Icons/grid-4col-ce.svg')
    ->setGroup('conn_layout')
);

$registry->configureContainer(
    (new ContainerConfiguration(
        'conn_grid_4_8',
        'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_4_8',
        'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_4_8.description',
        [
            [
                ['name' => 'Narrow (4)', 'colPos' => 230, 'colspan' => 4],
                ['name' => 'Wide (8)',   'colPos' => 231, 'colspan' => 8],
            ],
        ]
    ))
    ->setIcon('EXT:conn_t3wtk_elements/Resources/Public/Icons/grid-4-8-ce.svg')
    ->setGroup('conn_layout')
);

$registry->configureContainer(
    (new ContainerConfiguration(
        'conn_grid_8_4',
        'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_8_4',
        'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_8_4.description',
        [
            [
                ['name' => 'Wide (8)',   'colPos' => 240, 'colspan' => 8],
                ['name' => 'Narrow (4)', 'colPos' => 241, 'colspan' => 4],
            ],
        ]
    ))
    ->setIcon('EXT:conn_t3wtk_elements/Resources/Public/Icons/grid-8-4-ce.svg')
    ->setGroup('conn_layout')
);


// ─── Timeline ─────────────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_timeline'] = 'conn-ce-timeline';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.timeline',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.timeline.description',
    'value' => 'conn_timeline',
    'icon'  => 'conn-ce-timeline',
    'group' => 'conn_data',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_timeline_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:timeline.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_timeline_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 200,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_timeline_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:timeline.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/Timeline.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_timeline'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_timeline_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:timeline.tab.config,
            tx_conn_t3wtk_elements_timeline_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── People ─────────────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_people'] = 'conn-ce-people';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.people',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.people.description',
    'value' => 'conn_people',
    'icon'  => 'conn-ce-people',
    'group' => 'conn_data',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_people_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:people.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/People.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_people'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:people.tab.config,
            tx_conn_t3wtk_elements_people_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Text Icon ──────────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_text_icon'] = 'conn-ce-text-icon';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label'       => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.text_icon',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.text_icon.description',
    'value' => 'conn_text_icon',
    'icon'  => 'conn-ce-text-icon',
    'group' => 'default',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_text_icon_library' => [
        'label'    => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.library',
        'onChange' => 'reload',
        'config'   => [
            'type'       => 'select',
            'renderType' => 'selectSingle',
            'items'      => [
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.library.bootstrap', 'value' => 'bootstrap-icons'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.library.fontawesome', 'value' => 'font-awesome'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.library.ionicons', 'value' => 'ionicons'],
            ],
            'default'  => 'bootstrap-icons',
        ],
    ],
    'tx_conn_t3wtk_elements_text_icon_style' => [
        'label'       => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.style',
        'displayCond' => 'FIELD:tx_conn_t3wtk_elements_text_icon_library:!=:bootstrap-icons',
        'onChange'    => 'reload',
        'config'      => [
            'type'          => 'select',
            'renderType'    => 'connIconStyleSelect',
            'itemsProcFunc' => \Connetation\T3wtkElements\Service\IconItemsProcFunc::class . '->getIconStyleItems',
            'items'         => [],
            'default'       => 'solid',
        ],
    ],
    'tx_conn_t3wtk_elements_text_icon_name' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.name',
        'config' => [
            'type'          => 'select',
            'renderType'    => 'connIconSelect',
            'itemsProcFunc' => \Connetation\T3wtkElements\Service\IconItemsProcFunc::class . '->getIconNameItems',
            'items'         => [
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.name.empty', 'value' => ''],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_text_icon_position' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.position',
        'config' => [
            'type'       => 'select',
            'renderType' => 'selectSingle',
            'items'      => [
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.position.top', 'value' => 'top'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.position.left', 'value' => 'left'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.position.right', 'value' => 'right'],
            ],
            'default' => 'top',
        ],
    ],
    'tx_conn_t3wtk_elements_text_icon_shape' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.shape',
        'config' => [
            'type'       => 'select',
            'renderType' => 'selectSingle',
            'items'      => [
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.shape.none', 'value' => 'none'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.shape.square', 'value' => 'square'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.shape.circle', 'value' => 'circle'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.shape.hexagon', 'value' => 'hexagon'],
            ],
            'default' => 'none',
        ],
    ],
    'tx_conn_t3wtk_elements_text_icon_size' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.size',
        'config' => [
            'type'       => 'select',
            'renderType' => 'selectSingle',
            'items'      => [
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.size.small', 'value' => 'small'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.size.medium', 'value' => 'medium'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.size.large', 'value' => 'large'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.size.xlarge', 'value' => 'xlarge'],
                ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.size.xxlarge', 'value' => 'xxlarge'],
            ],
            'default' => 'medium',
        ],
    ],
    'tx_conn_t3wtk_elements_text_icon_color' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.color',
        'config' => [
            'type'       => 'input',
            'renderType' => 'color',
            'size'       => 10,
        ],
    ],
    'tx_conn_t3wtk_elements_text_icon_bg' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.bg',
        'config' => [
            'type'       => 'input',
            'renderType' => 'color',
            'size'       => 10,
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['palettes']['conn_text_icon_source'] = [
    'showitem' => 'tx_conn_t3wtk_elements_text_icon_library, tx_conn_t3wtk_elements_text_icon_style',
];

$GLOBALS['TCA']['tt_content']['palettes']['conn_text_icon_appearance'] = [
    'showitem' => 'tx_conn_t3wtk_elements_text_icon_position, tx_conn_t3wtk_elements_text_icon_shape, tx_conn_t3wtk_elements_text_icon_size, --linebreak--, tx_conn_t3wtk_elements_text_icon_color, tx_conn_t3wtk_elements_text_icon_bg',
];

$GLOBALS['TCA']['tt_content']['types']['conn_text_icon'] = [
    'columnsOverrides' => [
        'bodytext' => [
            'config' => [
                'enableRichtext' => true,
            ],
        ],
    ],
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,bodytext,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.tab.icon,
            --palette--;;conn_text_icon_source,
            tx_conn_t3wtk_elements_text_icon_name,
            --palette--;;conn_text_icon_appearance,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Map & Location ─────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_map'] = 'conn-ce-map';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.map',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.map.description',
    'value' => 'conn_map',
    'icon'  => 'conn-ce-map',
    'group' => 'conn_data',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_map_illustration_image' => [
        'label'       => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:map.illustration_image',
        'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:map.illustration_image.description',
        'config'      => [
            'type'     => 'file',
            'allowed'  => 'common-image-types',
            'maxitems' => 1,
        ],
    ],
    'tx_conn_t3wtk_elements_map_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:map.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/Map.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_map'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:media,
            tx_conn_t3wtk_elements_map_illustration_image,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:map.tab.config,
            tx_conn_t3wtk_elements_map_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];

// ─── Video Hero ─────────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_video_hero'] = 'conn-ce-video-hero';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.video_hero',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.video_hero.description',
    'value' => 'conn_video_hero',
    'icon'  => 'conn-ce-video-hero',
    'group' => 'conn_sliders',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_video_hero_media' => [
        'label'       => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:video_hero.media',
        'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:video_hero.media.description',
        'config'      => [
            'type'     => 'file',
            // Most standard formats only, not the full common-media-types
            // alias (28 extensions incl. audio/documents/images — way more
            // than a video field needs): mp4/webm/mov cover self-hosted,
            // youtube/vimeo cover online media.
            'allowed'  => 'mp4,webm,mov,youtube,vimeo',
            'maxitems' => 1,
        ],
    ],
    'tx_conn_t3wtk_elements_video_hero_poster' => [
        'label'       => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:video_hero.poster',
        'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:video_hero.poster.description',
        'config'      => [
            'type'     => 'file',
            // Most standard web image formats only, not the full
            // common-image-types alias (14 extensions incl. ai/psd/tif/pdf —
            // irrelevant for a poster image).
            'allowed'  => 'jpg,jpeg,png,webp,gif',
            'maxitems' => 1,
        ],
    ],
    'tx_conn_t3wtk_elements_video_hero_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:video_hero.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/VideoHero.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_video_hero'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:media,
            tx_conn_t3wtk_elements_video_hero_media, tx_conn_t3wtk_elements_video_hero_poster,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:video_hero.tab.config,
            tx_conn_t3wtk_elements_video_hero_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];


// ─── FlipCard ─────────────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['conn_flip_card'] = 'conn-ce-flip-card';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.flip_card',
    'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:ce.flip_card.description',
    'value' => 'conn_flip_card',
    'icon'  => 'conn-ce-flip-card',
    'group' => 'conn_interactive',
]);

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_t3wtk_elements_flip_card_items' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:flip_card.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_t3wtk_elements_domain_model_flip_card_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 200,
            'appearance'    => [
                'collapseAll'        => true,
                'levelLinksPosition' => 'top',
                'enabledControls'    => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_t3wtk_elements_flip_card_settings' => [
        'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:flip_card.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => 'FILE:EXT:conn_t3wtk_elements/Configuration/FlexForms/FlipCard.xml',
        ],
    ],
]);

$GLOBALS['TCA']['tt_content']['types']['conn_flip_card'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;headers,tx_conn_t3wtk_elements_flip_card_items,
        --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:flip_card.tab.config,
            tx_conn_t3wtk_elements_flip_card_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];
