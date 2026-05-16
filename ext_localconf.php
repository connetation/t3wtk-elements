<?php

declare(strict_types=1);

defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['WikiCode'] = 'EXT:conn_elements/Configuration/RTE/WikiCode.yaml';

$GLOBALS['TYPO3_CONF_VARS']['BE']['defaultPageTSconfig'] ??= '';
$GLOBALS['TYPO3_CONF_VARS']['BE']['defaultPageTSconfig'] .=
    'mod.wizards.newContentElement.wizardItems.conn.show = dashboard,wiki
     mod.wizards.newContentElement.wizardItems.conn.elements.dashboard {
        iconIdentifier = conn-ce-dashboard
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.dashboard
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.dashboard.description
        tt_content_defValues.CType = dashboard
    }
     mod.wizards.newContentElement.wizardItems.conn.elements.wiki {
        iconIdentifier = conn-ce-wiki
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.wiki
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.wiki.description
        tt_content_defValues.CType = wiki
    }';
