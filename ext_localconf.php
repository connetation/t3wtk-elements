<?php

declare(strict_types=1);

defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['WikiCode'] = 'EXT:conn_elements/Configuration/RTE/WikiCode.yaml';

$GLOBALS['TYPO3_CONF_VARS']['BE']['defaultPageTSconfig'] ??= '';
$GLOBALS['TYPO3_CONF_VARS']['BE']['defaultPageTSconfig'] .=
    'mod.wizards.newContentElement.wizardItems.conn.show = conn_dashboard,conn_wiki
     mod.wizards.newContentElement.wizardItems.conn.elements.conn_dashboard {
        iconIdentifier = conn-ce-dashboard
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.dashboard
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.dashboard.description
        tt_content_defValues.CType = conn_dashboard
    }
     mod.wizards.newContentElement.wizardItems.conn.elements.conn_wiki {
        iconIdentifier = conn-ce-wiki
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.wiki
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.wiki.description
        tt_content_defValues.CType = conn_wiki
    }
     mod.wizards.newContentElement.wizardItems.conn_bootstrap.header = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce_group.conn_bootstrap
     mod.wizards.newContentElement.wizardItems.conn_swiper.header = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce_group.conn_swiper
     mod.wizards.newContentElement.wizardItems.conn_swiper.show = conn_swiper_hero_slider,conn_swiper_slider,conn_swiper_thumbs_slider,conn_swiper_parallax_slider
     mod.wizards.newContentElement.wizardItems.conn_swiper.elements.conn_swiper_hero_slider {
        iconIdentifier = conn-ce-hero-slider
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_hero_slider
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_hero_slider.description
        tt_content_defValues.CType = conn_swiper_hero_slider
    }
     mod.wizards.newContentElement.wizardItems.conn_swiper.elements.conn_swiper_slider {
        iconIdentifier = conn-ce-slider
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_slider
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_slider.description
        tt_content_defValues.CType = conn_swiper_slider
    }
     mod.wizards.newContentElement.wizardItems.conn_swiper.elements.conn_swiper_thumbs_slider {
        iconIdentifier = conn-ce-thumbs-slider
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_thumbs_slider
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_thumbs_slider.description
        tt_content_defValues.CType = conn_swiper_thumbs_slider
    }
     mod.wizards.newContentElement.wizardItems.conn_swiper.elements.conn_swiper_parallax_slider {
        iconIdentifier = conn-ce-parallax-slider
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_parallax_slider
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.swiper_parallax_slider.description
        tt_content_defValues.CType = conn_swiper_parallax_slider
    }
     mod.wizards.newContentElement.wizardItems.conn_bootstrap.show = conn_bs_accordion,conn_bs_card_group
     mod.wizards.newContentElement.wizardItems.conn_bootstrap.elements.conn_bs_accordion {
        iconIdentifier = conn-ce-accordion
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_accordion
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_accordion.description
        tt_content_defValues.CType = conn_bs_accordion
    }
     mod.wizards.newContentElement.wizardItems.conn_bootstrap.elements.conn_bs_card_group {
        iconIdentifier = conn-ce-card-group
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_card_group
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_card_group.description
        tt_content_defValues.CType = conn_bs_card_group
    }';
