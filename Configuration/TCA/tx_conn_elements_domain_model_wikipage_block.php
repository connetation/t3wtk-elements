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
        'title'           => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:model.wikipage_block',
        'label'           => 'heading',
        'label_alt'       => 'type',
        'label_alt_force' => false,
        'sortby'          => 'sorting',
        'tstamp'          => 'tstamp',
        'crdate'          => 'crdate',
        'delete'          => 'deleted',
        'enablecolumns'   => ['disabled' => 'hidden'],
        'type'            => 'type',
        'typeicon_column' => 'type',
        'typeicon_classes' => [
            'text' => 'content-text',
            'code' => 'content-special-html',
        ],
        'rootLevel'       => -1,
        'security'        => ['ignorePageTypeRestriction' => true],
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
        'page' => [
            'config' => ['type' => 'passthrough'],
        ],
        'type' => [
            'label'    => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:block.type',
            'onChange' => 'reload',
            'config'   => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => [
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:block.type.text', 'value' => 'text'],
                    ['label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:block.type.code', 'value' => 'code'],
                ],
                'default' => 'text',
            ],
        ],
        'heading' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:block.heading',
            'config' => ['type' => 'input', 'size' => 50],
        ],
        'heading_layout' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:layout.heading',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => $headingLayoutItems,
                'default'    => 0,
            ],
        ],
        'content' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:block.content',
            'config' => [
                'type'                  => 'text',
                'enableRichtext'        => true,
                'richtextConfiguration' => 'WikiCode',
                'rows'                  => 10,
            ],
        ],
        'code_text' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:block.code_text',
            'config' => [
                'type'                  => 'text',
                'enableRichtext'        => true,
                'richtextConfiguration' => 'WikiCode',
                'rows'                  => 5,
            ],
        ],
        'code_language' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:block.code_language',
            'config' => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => [
                    ['label' => 'Plain text',   'value' => 'plaintext'],
                    ['label' => 'PHP',          'value' => 'php'],
                    ['label' => 'JavaScript',   'value' => 'javascript'],
                    ['label' => 'TypeScript',   'value' => 'typescript'],
                    ['label' => 'HTML',         'value' => 'html'],
                    ['label' => 'CSS / SCSS',   'value' => 'css'],
                    ['label' => 'SQL',          'value' => 'sql'],
                    ['label' => 'Shell / Bash', 'value' => 'bash'],
                    ['label' => 'JSON',         'value' => 'json'],
                    ['label' => 'YAML',         'value' => 'yaml'],
                    ['label' => 'XML',          'value' => 'xml'],
                    ['label' => 'Markdown',     'value' => 'markdown'],
                ],
                'default' => 'plaintext',
            ],
        ],
        'sorting' => ['config' => ['type' => 'passthrough']],
    ],
    'types' => [
        '0'    => ['showitem' => 'type, heading, heading_layout, content, --div--;Access, hidden'],
        'text' => ['showitem' => 'type, heading, heading_layout, content, --div--;Access, hidden'],
        'code' => [
            'showitem' => 'type, heading, heading_layout, code_text, code_language, content, --div--;Access, hidden',
            'columnsOverrides' => [
                'content' => [
                    'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:block.code_content',
                    'config' => [
                        'type'           => 'text',
                        'enableRichtext' => false,
                        'rows'           => 15,
                        'wrap'           => 'off',
                    ],
                ],
            ],
        ],
    ],
];
