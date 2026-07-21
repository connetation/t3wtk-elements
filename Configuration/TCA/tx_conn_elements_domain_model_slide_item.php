<?php

declare(strict_types=1);

// Same value set as TYPO3 core's native tt_content.header_layout: 0=default,
// 1-5=H1-H5, 100=hidden. Reused for every heading/subheading layout column.
// Labels reuse conn_base's relabeled H1-H5 strings (not core's "Layout N"),
// so this matches the same convention across every content element.
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
        'title'         => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:model.slide_item',
        'label'         => 'title',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'versioningWS'        => true,
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_elements/Resources/Public/Icons/slider-ce.svg',
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
        'title' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.title',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'max'  => 255,
                'eval' => 'trim',
            ],
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
        'subtitle' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.subtitle',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'max'  => 255,
                'eval' => 'trim',
            ],
        ],
        'subtitle_layout' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:layout.subheading',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingLayoutItems,
                'default'    => 0,
            ],
        ],
        'body' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.body',
            'config' => [
                'type'           => 'text',
                'enableRichtext' => true,
                'rows'           => 5,
            ],
        ],
        'image' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.image',
            'config' => [
                'type'     => 'file',
                'allowed'  => 'jpg,jpeg,png,gif,webp,svg',
                'maxitems' => 1,
                'overrideChildTca' => [
                    'columns' => [
                        'crop' => [
                            'config' => [
                                'cropVariants' => [
                                    'default' => [
                                        'title' => 'Default (>=1400px)',
                                        'allowedAspectRatios' => [
                                            'free' => [
                                                'title' => 'Free',
                                                'value' => 0.0,
                                            ],
                                        ],
                                        'selectedRatio' => 'free',
                                    ],
                                    'xl' => [
                                        'title' => 'Extra Large (1200-1399px)',
                                        'allowedAspectRatios' => [
                                            'free' => [
                                                'title' => 'Free',
                                                'value' => 0.0,
                                            ],
                                        ],
                                        'selectedRatio' => 'free',
                                    ],
                                    'lg' => [
                                        'title' => 'Large (992-1199px)',
                                        'allowedAspectRatios' => [
                                            'free' => [
                                                'title' => 'Free',
                                                'value' => 0.0,
                                            ],
                                        ],
                                        'selectedRatio' => 'free',
                                    ],
                                    'md' => [
                                        'title' => 'Medium (768-991px)',
                                        'allowedAspectRatios' => [
                                            'free' => [
                                                'title' => 'Free',
                                                'value' => 0.0,
                                            ],
                                        ],
                                        'selectedRatio' => 'free',
                                    ],
                                    'sm' => [
                                        'title' => 'Small (576-767px)',
                                        'allowedAspectRatios' => [
                                            'free' => [
                                                'title' => 'Free',
                                                'value' => 0.0,
                                            ],
                                        ],
                                        'selectedRatio' => 'free',
                                    ],
                                    'base' => [
                                        'title' => 'Base (<576px)',
                                        'allowedAspectRatios' => [
                                            'free' => [
                                                'title' => 'Free',
                                                'value' => 0.0,
                                            ],
                                            'portrait' => [
                                                'title' => 'Portrait (3:4)',
                                                'value' => 0.75,
                                            ],
                                        ],
                                        'selectedRatio' => 'portrait',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'link' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.link',
            'config' => [
                'type'         => 'link',
                'allowedTypes' => ['page', 'url', 'email', 'telephone'],
            ],
        ],
        'link_label' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.link_label',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max'  => 100,
                'eval' => 'trim',
            ],
        ],
        'text_animation' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_animation',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'default'    => '',
                'items'      => [
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_animation.none',   'value' => ''],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_animation.words',  'value' => 'words'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_animation.lines',  'value' => 'lines'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_animation.chars',  'value' => 'chars'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_animation.reveal', 'value' => 'reveal'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_animation.fuzzy',  'value' => 'fuzzy'],
                ],
            ],
        ],
        'bg_animation' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:bg_animation',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'default'    => '',
                'items'      => [
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:bg_animation.none', 'value' => ''],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:bg_animation.zoom', 'value' => 'zoom'],
                ],
            ],
        ],
        'animation_duration' => [
            'label'       => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:animation_duration',
            'description' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:animation_duration.description',
            'config'      => [
                'type'    => 'number',
                'default' => 0,
                'range'   => ['lower' => 0, 'upper' => 5000],
            ],
        ],
        'text_pos_v' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_v',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'default'    => '',
                'items'      => [
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_v.bottom', 'value' => ''],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_v.top',    'value' => 'top'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_v.center', 'value' => 'center'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_v.bottom_explicit', 'value' => 'bottom'],
                ],
            ],
        ],
        'text_pos_h' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_h',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'default'    => '',
                'items'      => [
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_h.left',   'value' => ''],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_h.center', 'value' => 'center'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_h.right',  'value' => 'right'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:text_pos_h.left_explicit', 'value' => 'left'],
                ],
            ],
        ],
        // Per-slide color overrides — each is a light/dark pair since these
        // sit on a background image whose readability differs from the
        // site's own light/dark mode. Left empty, the slide keeps its
        // built-in default look (white title, accent eyebrow/button).
        // opacity:true enables an alpha channel in the color picker.
        'title_color_light' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.title_color_light',
            'config' => ['type' => 'color', 'opacity' => true],
        ],
        'title_color_dark' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.title_color_dark',
            'config' => ['type' => 'color', 'opacity' => true],
        ],
        'subtitle_color_light' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.subtitle_color_light',
            'config' => ['type' => 'color', 'opacity' => true],
        ],
        'subtitle_color_dark' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.subtitle_color_dark',
            'config' => ['type' => 'color', 'opacity' => true],
        ],
        'button_color_light' => [
            'label'       => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.button_color_light',
            'description' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.button_color.description',
            'config'      => ['type' => 'color', 'opacity' => true],
        ],
        'button_color_dark' => [
            'label'       => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.button_color_dark',
            'description' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.button_color.description',
            'config'      => ['type' => 'color', 'opacity' => true],
        ],
        // Independent of the background above — the button's default (Bootstrap
        // .btn-primary) text color doesn't auto-contrast against a custom
        // background, so a light background + the default white text is
        // unreadable unless the editor can also set the text color.
        'button_text_color_light' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.button_text_color_light',
            'config' => ['type' => 'color', 'opacity' => true],
        ],
        'button_text_color_dark' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.button_text_color_dark',
            'config' => ['type' => 'color', 'opacity' => true],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    title, title_layout, subtitle, subtitle_layout, body, image,
                --div--;LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:slide_item.tab.link,
                    link, link_label,
                    --linebreak--,
                    button_color_light, button_color_dark,
                    button_text_color_light, button_text_color_dark,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
                    text_animation, animation_duration,
                    text_pos_v, text_pos_h,
                    bg_animation,
                    --linebreak--,
                    title_color_light, title_color_dark,
                    subtitle_color_light, subtitle_color_dark,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
];
