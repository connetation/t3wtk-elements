<?php

declare(strict_types=1);

/*
 * This file is part of the Connetation TYPO3 Web Toolkit - T3WTK.
 *
 * Copyright (c) Connetation Web Engineering GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
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

use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

// ─── Card fields – rendered by the "Subpage Cards" content element ──
// The card title is always the page title. Teaser and media fall back to
// the page's own "abstract" and "media" fields, the link falls back to the
// page itself (SubpageCardsProcessor).
ExtensionManagementUtility::addTCAcolumns('pages', [
    'tx_conn_t3wtk_elements_card_subtitle' => [
        'exclude'     => true,
        'label'       => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pages.card_subtitle',
        'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pages.card_subtitle.description',
        'config'      => [
            'type' => 'input',
            'size' => 50,
            'max'  => 255,
            'eval' => 'trim',
        ],
    ],
    'tx_conn_t3wtk_elements_card_teaser' => [
        'exclude'     => true,
        'label'       => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pages.card_teaser',
        'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pages.card_teaser.description',
        'config'      => [
            'type'           => 'text',
            'enableRichtext' => true,
            'rows'           => 5,
        ],
    ],
    'tx_conn_t3wtk_elements_card_link' => [
        'exclude'     => true,
        'label'       => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pages.card_link',
        'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pages.card_link.description',
        'config'      => [
            'type' => 'link',
        ],
    ],
    'tx_conn_t3wtk_elements_card_media' => [
        'exclude'     => true,
        'label'       => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pages.card_media',
        'description' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pages.card_media.description',
        'config'      => [
            'type'     => 'file',
            'allowed'  => 'common-image-types',
            'maxitems' => 1,
        ],
    ],
]);

ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    '--div--;LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:pages.tab.card,
        tx_conn_t3wtk_elements_card_subtitle,tx_conn_t3wtk_elements_card_teaser,tx_conn_t3wtk_elements_card_link,tx_conn_t3wtk_elements_card_media',
    // Only the doktypes the element renders as cards
    implode(',', [
        PageRepository::DOKTYPE_DEFAULT,
        PageRepository::DOKTYPE_LINK,
        PageRepository::DOKTYPE_SHORTCUT,
    ]),
    ''
);
