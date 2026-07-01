<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title'         => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:model.pricing_tier',
        'label'         => 'name',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_elements/Resources/Public/Icons/pricing-ce.svg',
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
        'name' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.name',
            'config' => [
                'type'     => 'input',
                'size'     => 40,
                'max'      => 100,
                'eval'     => 'trim',
                'required' => true,
            ],
        ],
        'tagline' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.tagline',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'max'  => 255,
                'eval' => 'trim',
            ],
        ],
        'currency' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.currency',
            'config' => [
                'type'    => 'input',
                'size'    => 5,
                'max'     => 5,
                'eval'    => 'trim',
                'default' => '€',
            ],
        ],
        'amount' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.amount',
            'config' => [
                'type' => 'input',
                'size' => 15,
                'max'  => 30,
                'eval' => 'trim',
            ],
        ],
        'period' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.period',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'max'  => 50,
                'eval' => 'trim',
            ],
        ],
        'price_note' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.price_note',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'max'  => 255,
                'eval' => 'trim',
            ],
        ],
        'features' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.features',
            'config' => [
                'type' => 'text',
                'rows' => 8,
                'placeholder' => 'One feature per line. Prefix with - to mark as unavailable.',
            ],
        ],
        'cta_label' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.cta_label',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max'  => 100,
                'eval' => 'trim',
            ],
        ],
        'cta_link' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.cta_link',
            'config' => [
                'type'         => 'link',
                'allowedTypes' => ['page', 'url', 'email'],
            ],
        ],
        'featured' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.featured',
            'config' => [
                'type'       => 'check',
                'renderType' => 'checkboxToggle',
                'items'      => [['label' => '']],
            ],
        ],
        'badge_text' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.badge_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max'  => 60,
                'eval' => 'trim',
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    name, tagline, currency, amount, period, price_note, features,
                --div--;LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.tab.cta,
                    cta_label, cta_link,
                --div--;LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:pricing_tier.tab.highlight,
                    featured, badge_text,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
];
