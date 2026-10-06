<?php

declare(strict_types=1);

// Same value set as TYPO3 core's native tt_content.header_layout: 0=default,
// 1-5=H1-H5, 100=hidden. Reused for every heading/subheading layout column.
$headingLayoutItems = [
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_layout.default', 'value' => 0],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_layout.h1', 'value' => 1],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_layout.h2', 'value' => 2],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_layout.h3', 'value' => 3],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_layout.h4', 'value' => 4],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_layout.h5', 'value' => 5],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_layout.hidden', 'value' => 100],
];

// Visual size, independent of the tag *_layout picks — lets an editor keep
// the SEO-correct heading level while styling it like a different size
// (e.g. an <h3> that looks like an .h5). Same pattern/labels as conn_t3wtk_base's
// tt_content header_class/subheader_class. Reused for every *_class column.
$headingClassItems = [
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.none', 'value' => ''],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.h1', 'value' => 'h1'],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.h2', 'value' => 'h2'],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.h3', 'value' => 'h3'],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.h4', 'value' => 'h4'],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.h5', 'value' => 'h5'],
    ['label' => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class.hidden', 'value' => 'visually-hidden'],
];

return [
    'ctrl' => [
        'title'         => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:model.feature_item',
        'label'         => 'title',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'versioningWS'        => true,
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_t3wtk_elements/Resources/Public/Icons/feature-grid-ce.svg',
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
        // Same dynamic icon picker as conn_text_icon (library + style + name,
        // Bootstrap Icons/Font Awesome/Ionicons) — a curated static list here
        // previously went stale: any icon in use that wasn't in that
        // hand-picked subset showed as "Missing label" in the backend and got
        // silently wiped to empty on the next unrelated save, because the
        // plain <select> had no matching <option> to resubmit. Stored as a
        // bare icon name (e.g. "tools"), not a full CSS class, same as every
        // other table using this picker (process_step, conn_text_icon) — the
        // Fluid partial prefixes it per-library at render time.
        'icon_library' => [
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
                'default'    => 'bootstrap-icons',
            ],
        ],
        'icon_style' => [
            'label'       => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.style',
            'displayCond' => 'FIELD:icon_library:!=:bootstrap-icons',
            'onChange'    => 'reload',
            'config'      => [
                'type'          => 'select',
                'renderType'    => 'connIconStyleSelect',
                'itemsProcFunc' => \Connetation\T3wtkElements\Service\IconItemsProcFunc::class . '->getIconStyleItems',
                'items'         => [],
                'default'       => 'solid',
                'iconLibraryField' => 'icon_library',
            ],
        ],
        'icon' => [
            'label'       => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:feature_item.icon',
            'config'      => [
                'type'              => 'select',
                'renderType'        => 'connIconSelect',
                'itemsProcFunc'     => \Connetation\T3wtkElements\Service\IconItemsProcFunc::class . '->getIconNameItems',
                'iconLibraryField'  => 'icon_library',
                'iconStyleField'    => 'icon_style',
                'items'             => [
                    ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.name.empty', 'value' => ''],
                ],
            ],
        ],
        'image' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:feature_item.image',
            'config' => [
                'type'     => 'file',
                'allowed'  => 'jpg,jpeg,png,gif,webp,svg',
                'maxitems' => 1,
            ],
        ],
        'title' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:feature_item.title',
            'config' => [
                'type'     => 'input',
                'size'     => 60,
                'max'      => 255,
                'eval'     => 'trim',
                'required' => true,
            ],
        ],
        'title_layout' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:layout.heading',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingLayoutItems,
                'default'    => 0,
            ],
        ],
        'title_class' => [
            'label'  => 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:header_class',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingClassItems,
                'default'    => '',
            ],
        ],
        'body' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:feature_item.body',
            'config' => [
                'type'           => 'text',
                'enableRichtext' => true,
                'rows'           => 4,
            ],
        ],
        'link' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:feature_item.link',
            'config' => [
                'type'         => 'link',
                'allowedTypes' => ['page', 'url'],
            ],
        ],
        'link_label' => [
            'label'  => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:feature_item.link_label',
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
                    icon_library, icon_style, icon, image, title, title_layout, title_class, body,
                --div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:feature_item.tab.link,
                    link, link_label,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
];
