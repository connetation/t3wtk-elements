<?php

declare(strict_types=1);

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

// Visual size, independent of the tag *_layout picks — lets an editor keep
// the SEO-correct heading level while styling it like a different size
// (e.g. an <h3> that looks like an .h5). Same pattern/labels as conn_base's
// tt_content header_class/subheader_class. Reused for every *_class column.
$headingClassItems = [
    ['label' => 'LLL:EXT:conn_base/Resources/Private/Language/locallang_be.xlf:header_class.none', 'value' => ''],
    ['label' => 'LLL:EXT:conn_base/Resources/Private/Language/locallang_be.xlf:header_class.h1', 'value' => 'h1'],
    ['label' => 'LLL:EXT:conn_base/Resources/Private/Language/locallang_be.xlf:header_class.h2', 'value' => 'h2'],
    ['label' => 'LLL:EXT:conn_base/Resources/Private/Language/locallang_be.xlf:header_class.h3', 'value' => 'h3'],
    ['label' => 'LLL:EXT:conn_base/Resources/Private/Language/locallang_be.xlf:header_class.h4', 'value' => 'h4'],
    ['label' => 'LLL:EXT:conn_base/Resources/Private/Language/locallang_be.xlf:header_class.h5', 'value' => 'h5'],
    ['label' => 'LLL:EXT:conn_base/Resources/Private/Language/locallang_be.xlf:header_class.hidden', 'value' => 'visually-hidden'],
];

return [
    'ctrl' => [
        'title'         => 'LLL:EXT:conn_t3kit_elements/Resources/Private/Language/locallang_be.xlf:model.feature_item',
        'label'         => 'title',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'versioningWS'        => true,
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_t3kit_elements/Resources/Public/Icons/feature-grid-ce.svg',
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
        'icon' => [
            'label'  => 'LLL:EXT:conn_t3kit_elements/Resources/Private/Language/locallang_be.xlf:feature_item.icon',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'minitems'   => 0,
                'maxitems'   => 1,
                'items'      => [
                    ['label' => '— No icon —', 'value' => ''],
                    ['label' => '── General ──────────────────', 'value' => '--div--'],
                    ['label' => 'Check circle', 'value' => 'bi bi-check-circle'],
                    ['label' => 'Check circle (filled)', 'value' => 'bi bi-check-circle-fill'],
                    ['label' => 'Star', 'value' => 'bi bi-star'],
                    ['label' => 'Star (filled)', 'value' => 'bi bi-star-fill'],
                    ['label' => 'Lightning', 'value' => 'bi bi-lightning'],
                    ['label' => 'Lightning (filled)', 'value' => 'bi bi-lightning-fill'],
                    ['label' => 'Rocket', 'value' => 'bi bi-rocket'],
                    ['label' => 'Rocket (filled)', 'value' => 'bi bi-rocket-fill'],
                    ['label' => 'Trophy', 'value' => 'bi bi-trophy'],
                    ['label' => 'Award', 'value' => 'bi bi-award'],
                    ['label' => 'Heart', 'value' => 'bi bi-heart'],
                    ['label' => 'Heart (filled)', 'value' => 'bi bi-heart-fill'],
                    ['label' => '── Security ─────────────────', 'value' => '--div--'],
                    ['label' => 'Shield check', 'value' => 'bi bi-shield-check'],
                    ['label' => 'Shield (filled)', 'value' => 'bi bi-shield-fill'],
                    ['label' => 'Lock', 'value' => 'bi bi-lock'],
                    ['label' => 'Lock (filled)', 'value' => 'bi bi-lock-fill'],
                    ['label' => 'Key', 'value' => 'bi bi-key'],
                    ['label' => 'Eye', 'value' => 'bi bi-eye'],
                    ['label' => '── Technology ───────────────', 'value' => '--div--'],
                    ['label' => 'Code', 'value' => 'bi bi-code-slash'],
                    ['label' => 'CPU', 'value' => 'bi bi-cpu'],
                    ['label' => 'Cloud', 'value' => 'bi bi-cloud'],
                    ['label' => 'Cloud check', 'value' => 'bi bi-cloud-check'],
                    ['label' => 'Database', 'value' => 'bi bi-database'],
                    ['label' => 'HDD', 'value' => 'bi bi-hdd'],
                    ['label' => 'Wi-Fi', 'value' => 'bi bi-wifi'],
                    ['label' => 'Layers', 'value' => 'bi bi-layers'],
                    ['label' => '── Business ─────────────────', 'value' => '--div--'],
                    ['label' => 'Briefcase', 'value' => 'bi bi-briefcase'],
                    ['label' => 'Graph up', 'value' => 'bi bi-graph-up'],
                    ['label' => 'Graph up arrow', 'value' => 'bi bi-graph-up-arrow'],
                    ['label' => 'Currency dollar', 'value' => 'bi bi-currency-dollar'],
                    ['label' => 'Cash coin', 'value' => 'bi bi-cash-coin'],
                    ['label' => 'Box', 'value' => 'bi bi-box'],
                    ['label' => 'Boxes', 'value' => 'bi bi-boxes'],
                    ['label' => '── Communication ────────────', 'value' => '--div--'],
                    ['label' => 'Envelope', 'value' => 'bi bi-envelope'],
                    ['label' => 'Chat dots', 'value' => 'bi bi-chat-dots'],
                    ['label' => 'Chat quote', 'value' => 'bi bi-chat-quote'],
                    ['label' => 'Telephone', 'value' => 'bi bi-telephone'],
                    ['label' => 'Bell', 'value' => 'bi bi-bell'],
                    ['label' => '── People ───────────────────', 'value' => '--div--'],
                    ['label' => 'People', 'value' => 'bi bi-people'],
                    ['label' => 'Person check', 'value' => 'bi bi-person-check'],
                    ['label' => 'Person heart', 'value' => 'bi bi-person-heart'],
                    ['label' => 'Globe', 'value' => 'bi bi-globe'],
                    ['label' => '── Tools ────────────────────', 'value' => '--div--'],
                    ['label' => 'Gear', 'value' => 'bi bi-gear'],
                    ['label' => 'Tools', 'value' => 'bi bi-tools'],
                    ['label' => 'Wrench', 'value' => 'bi bi-wrench'],
                    ['label' => 'Palette', 'value' => 'bi bi-palette'],
                    ['label' => 'Brush', 'value' => 'bi bi-brush'],
                    ['label' => 'Magic', 'value' => 'bi bi-magic'],
                ],
            ],
        ],
        'image' => [
            'label'  => 'LLL:EXT:conn_t3kit_elements/Resources/Private/Language/locallang_be.xlf:feature_item.image',
            'config' => [
                'type'     => 'file',
                'allowed'  => 'jpg,jpeg,png,gif,webp,svg',
                'maxitems' => 1,
            ],
        ],
        'title' => [
            'label'  => 'LLL:EXT:conn_t3kit_elements/Resources/Private/Language/locallang_be.xlf:feature_item.title',
            'config' => [
                'type'     => 'input',
                'size'     => 60,
                'max'      => 255,
                'eval'     => 'trim',
                'required' => true,
            ],
        ],
        'title_layout' => [
            'label'  => 'LLL:EXT:conn_t3kit_elements/Resources/Private/Language/locallang_be.xlf:layout.heading',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingLayoutItems,
                'default'    => 0,
            ],
        ],
        'title_class' => [
            'label'  => 'LLL:EXT:conn_base/Resources/Private/Language/locallang_be.xlf:header_class',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingClassItems,
                'default'    => '',
            ],
        ],
        'body' => [
            'label'  => 'LLL:EXT:conn_t3kit_elements/Resources/Private/Language/locallang_be.xlf:feature_item.body',
            'config' => [
                'type'           => 'text',
                'enableRichtext' => true,
                'rows'           => 4,
            ],
        ],
        'link' => [
            'label'  => 'LLL:EXT:conn_t3kit_elements/Resources/Private/Language/locallang_be.xlf:feature_item.link',
            'config' => [
                'type'         => 'link',
                'allowedTypes' => ['page', 'url'],
            ],
        ],
        'link_label' => [
            'label'  => 'LLL:EXT:conn_t3kit_elements/Resources/Private/Language/locallang_be.xlf:feature_item.link_label',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max'  => 100,
                'eval' => 'trim',
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    icon, image, title, title_layout, title_class, body,
                --div--;LLL:EXT:conn_t3kit_elements/Resources/Private/Language/locallang_be.xlf:feature_item.tab.link,
                    link, link_label,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
];
