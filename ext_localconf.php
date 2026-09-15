<?php

declare(strict_types=1);

defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1752000001] = [
    'nodeName' => 'connIconSelect',
    'priority'  => 40,
    'class'     => \Connetation\Elements\Form\Element\IconPreviewSelectElement::class,
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1752000002] = [
    'nodeName' => 'connIconStyleSelect',
    'priority'  => 40,
    'class'     => \Connetation\Elements\Form\Element\IconStyleSelectElement::class,
];

$GLOBALS['TYPO3_CONF_VARS']['BE']['defaultPageTSconfig'] ??= '';
$GLOBALS['TYPO3_CONF_VARS']['BE']['defaultPageTSconfig'] .=
    'mod.wizards.newContentElement.wizardItems.conn.show = conn_card_stack,conn_flip_carousel,conn_flip_gallery,conn_mosaic_gallery,conn_bs_timeline,conn_bs_stats,conn_bs_logo_wall,conn_bs_testimonials,conn_bs_pricing,conn_bs_feature_grid,conn_people,conn_quote,conn_alert
     mod.wizards.newContentElement.wizardItems.conn.elements.conn_card_stack {
        iconIdentifier = conn-ce-card-stack
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.card_stack
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.card_stack.description
        tt_content_defValues.CType = conn_card_stack
    }
     mod.wizards.newContentElement.wizardItems.conn.elements.conn_flip_carousel {
        iconIdentifier = conn-ce-flip-carousel
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.flip_carousel
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.flip_carousel.description
        tt_content_defValues.CType = conn_flip_carousel
    }
     mod.wizards.newContentElement.wizardItems.conn.elements.conn_flip_gallery {
        iconIdentifier = conn-ce-flip-gallery
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.flip_gallery
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.flip_gallery.description
        tt_content_defValues.CType = conn_flip_gallery
    }
     mod.wizards.newContentElement.wizardItems.conn.elements.conn_mosaic_gallery {
        iconIdentifier = conn-ce-mosaic-gallery
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.mosaic_gallery
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.mosaic_gallery.description
        tt_content_defValues.CType = conn_mosaic_gallery
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
     mod.wizards.newContentElement.wizardItems.conn_grid.header = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce_group.conn_grid
     mod.wizards.newContentElement.wizardItems.conn_grid.show = conn_grid_2col,conn_grid_3col,conn_grid_4col,conn_grid_4_8,conn_grid_8_4
     mod.wizards.newContentElement.wizardItems.conn_grid.elements.conn_grid_2col {
        iconIdentifier = conn_grid_2col
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_2col
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_2col.description
        tt_content_defValues.CType = conn_grid_2col
    }
     mod.wizards.newContentElement.wizardItems.conn_grid.elements.conn_grid_3col {
        iconIdentifier = conn_grid_3col
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_3col
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_3col.description
        tt_content_defValues.CType = conn_grid_3col
    }
     mod.wizards.newContentElement.wizardItems.conn_grid.elements.conn_grid_4col {
        iconIdentifier = conn_grid_4col
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_4col
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_4col.description
        tt_content_defValues.CType = conn_grid_4col
    }
     mod.wizards.newContentElement.wizardItems.conn_grid.elements.conn_grid_4_8 {
        iconIdentifier = conn_grid_4_8
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_4_8
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_4_8.description
        tt_content_defValues.CType = conn_grid_4_8
    }
     mod.wizards.newContentElement.wizardItems.conn_grid.elements.conn_grid_8_4 {
        iconIdentifier = conn_grid_8_4
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_8_4
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.grid_8_4.description
        tt_content_defValues.CType = conn_grid_8_4
    }
     mod.wizards.newContentElement.wizardItems.conn_bootstrap.show = conn_bs_accordion,conn_bs_card_group,conn_bs_carousel,conn_bs_tabs,conn_bs_process,conn_bs_before_after
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
    }
     mod.wizards.newContentElement.wizardItems.conn_bootstrap.elements.conn_bs_carousel {
        iconIdentifier = conn-ce-bs-carousel
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_carousel
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_carousel.description
        tt_content_defValues.CType = conn_bs_carousel
    }
     mod.wizards.newContentElement.wizardItems.conn_bootstrap.elements.conn_bs_tabs {
        iconIdentifier = conn-ce-tabs
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_tabs
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_tabs.description
        tt_content_defValues.CType = conn_bs_tabs
    }
     mod.wizards.newContentElement.wizardItems.conn_bootstrap.elements.conn_bs_process {
        iconIdentifier = conn-ce-process
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_process
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_process.description
        tt_content_defValues.CType = conn_bs_process
    }
     mod.wizards.newContentElement.wizardItems.conn_bootstrap.elements.conn_bs_before_after {
        iconIdentifier = conn-ce-before-after
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_before_after
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_before_after.description
        tt_content_defValues.CType = conn_bs_before_after
    }
     mod.wizards.newContentElement.wizardItems.conn.elements.conn_bs_timeline {
        iconIdentifier = conn-ce-timeline
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.timeline
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.timeline.description
        tt_content_defValues.CType = conn_bs_timeline
    }
     mod.wizards.newContentElement.wizardItems.conn.elements.conn_bs_stats {
        iconIdentifier = conn-ce-stats
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_stats
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_stats.description
        tt_content_defValues.CType = conn_bs_stats
    }
     mod.wizards.newContentElement.wizardItems.conn.elements.conn_bs_logo_wall {
        iconIdentifier = conn-ce-logo-wall
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_logo_wall
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_logo_wall.description
        tt_content_defValues.CType = conn_bs_logo_wall
    }
     mod.wizards.newContentElement.wizardItems.conn.elements.conn_bs_testimonials {
        iconIdentifier = conn-ce-testimonials
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_testimonials
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_testimonials.description
        tt_content_defValues.CType = conn_bs_testimonials
    }
     mod.wizards.newContentElement.wizardItems.conn.elements.conn_bs_pricing {
        iconIdentifier = conn-ce-pricing
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_pricing
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_pricing.description
        tt_content_defValues.CType = conn_bs_pricing
    }
     mod.wizards.newContentElement.wizardItems.conn.elements.conn_bs_feature_grid {
        iconIdentifier = conn-ce-feature-grid
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_feature_grid
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.bs_feature_grid.description
        tt_content_defValues.CType = conn_bs_feature_grid
    }
     mod.wizards.newContentElement.wizardItems.conn.elements.conn_people {
        iconIdentifier = conn-ce-people
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.people
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.people.description
        tt_content_defValues.CType = conn_people
    }
     mod.wizards.newContentElement.wizardItems.conn.elements.conn_quote {
        iconIdentifier = conn-ce-quote
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.quote
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.quote.description
        tt_content_defValues.CType = conn_quote
    }
     mod.wizards.newContentElement.wizardItems.conn.elements.conn_alert {
        iconIdentifier = conn-ce-alert
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.alert
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.alert.description
        tt_content_defValues.CType = conn_alert
    }
     mod.wizards.newContentElement.wizardItems.default.show := addToList(conn_text_column,conn_text_icon)
     mod.wizards.newContentElement.wizardItems.default.elements.conn_text_column {
        iconIdentifier = content-text-columns
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.text_column
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.text_column.description
        tt_content_defValues.CType = conn_text_column
    }
     mod.wizards.newContentElement.wizardItems.default.elements.conn_text_icon {
        iconIdentifier = conn-ce-text-icon
        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.text_icon
        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.text_icon.description
        tt_content_defValues.CType = conn_text_icon
    }';
