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

namespace Connetation\T3wtkElements\DataProcessing;

use Connetation\T3wtkBase\DataProcessing\AbstractItemsProcessor;
use Connetation\T3wtkBase\Utility\FontWeightOptions;
use Connetation\T3wtkBase\Utility\RadiusOptions;
use Connetation\T3wtkElements\Utility\HeroHeight;
use Connetation\T3wtkBase\Utility\ResponsiveValue;

final class SlideItemsProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_t3wtk_elements_domain_model_slide_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'slides';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return $processorConfiguration['settingsField'] ?? '';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function finalizeProcessedData(
        array $processedData,
        array $rows,
        array $settings,
        string $settingsField,
        int $contentElementUid
    ): array {
        // ─── Parallax slider: background image on tt_content ─────
        if (str_contains($settingsField, 'parallax')) {
            $grouped = $this->resolveFileReferences(
                'tt_content',
                'tx_conn_t3wtk_elements_parallax_slider_bg_image',
                [$contentElementUid]
            );
            $processedData['parallaxBgImage'] = $grouped[$contentElementUid][0] ?? null;
        }

        return $processedData;
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $allowedDelays = [3000, 4000, 5000, 7000];

        if (str_contains($settingsField, 'hero')) {
            $delay = (int)($raw['autoplay_delay'] ?? 4000);
            return [
                ...HeroHeight::resolve($raw['height'] ?? null),
                'effect'          => in_array($raw['effect'] ?? 'fade', ['fade', 'slide'], true)
                                        ? ($raw['effect'] ?? 'fade') : 'fade',
                'autoplay'        => (bool)($raw['autoplay'] ?? true),
                'autoplay_delay'  => in_array($delay, $allowedDelays, true) ? $delay : 4000,
                'show_navigation' => (bool)($raw['show_navigation'] ?? true),
                'hide_navigation_mobile' => (bool)($raw['hide_navigation_mobile'] ?? false),
                'show_pagination' => (bool)($raw['show_pagination'] ?? true),
                'hide_pagination_mobile' => (bool)($raw['hide_pagination_mobile'] ?? false),
            ];
        }

        if (str_contains($settingsField, 'thumbs')) {
            // Per device ("desktop,tablet,mobile"); empty = same as the next larger device.
            $thumbsPerView = ResponsiveValue::inherited(ResponsiveValue::parse($raw['thumbs_per_view'] ?? null, 2, 8, 4));
            $delay = (int)($raw['autoplay_delay'] ?? 4000);
            return [
                // Per device (vh); former medium/large keywords still work.
                ...HeroHeight::resolve($raw['height'] ?? null, 70),
                'thumbs_per_view' => $thumbsPerView['desktop'],
                'thumbs_per_view_tablet' => $thumbsPerView['tablet'],
                'thumbs_per_view_mobile' => $thumbsPerView['mobile'],
                'show_navigation' => (bool)($raw['show_navigation'] ?? true),
                'hide_navigation_mobile' => (bool)($raw['hide_navigation_mobile'] ?? false),
                'autoplay'        => (bool)($raw['autoplay'] ?? false),
                'autoplay_delay'  => in_array($delay, $allowedDelays, true) ? $delay : 4000,
                'openInLightbox'  => (bool)($raw['openInLightbox'] ?? false),
                'borderRadius'    => RadiusOptions::resolve($raw['borderRadius'] ?? null),
            ];
        }

        if (str_contains($settingsField, 'parallax')) {
            $delay = (int)($raw['autoplay_delay'] ?? 4000);
            return [
                // Per device (vh); former medium/large/fullscreen keywords still work.
                ...HeroHeight::resolve($raw['height'] ?? null, 70),
                'show_navigation' => (bool)($raw['show_navigation'] ?? true),
                'hide_navigation_mobile' => (bool)($raw['hide_navigation_mobile'] ?? false),
                'show_pagination' => (bool)($raw['show_pagination'] ?? true),
                'hide_pagination_mobile' => (bool)($raw['hide_pagination_mobile'] ?? false),
                'autoplay'        => (bool)($raw['autoplay'] ?? false),
                'autoplay_delay'  => in_array($delay, $allowedDelays, true) ? $delay : 4000,
            ];
        }

        // Per device ("desktop,tablet,mobile"); empty tablet/mobile (0) = automatic.
        $slidesPerView = ResponsiveValue::parse($raw['slides_per_view'] ?? null, 1, 4, 3);
        $delay = (int)($raw['autoplay_delay'] ?? 4000);

        return [
            'slides_per_view' => $slidesPerView['desktop'],
            'slides_per_view_tablet' => $slidesPerView['tablet'] ?? 0,
            'slides_per_view_mobile' => $slidesPerView['mobile'] ?? 0,
            'loop'            => (bool)($raw['loop'] ?? true),
            'autoplay'        => (bool)($raw['autoplay'] ?? false),
            'autoplay_delay'  => in_array($delay, $allowedDelays, true) ? $delay : 4000,
            'show_navigation' => (bool)($raw['show_navigation'] ?? true),
            'hide_navigation_mobile' => (bool)($raw['hide_navigation_mobile'] ?? false),
            'show_pagination' => (bool)($raw['show_pagination'] ?? true),
            'hide_pagination_mobile' => (bool)($raw['hide_pagination_mobile'] ?? false),
        ] + FontWeightOptions::resolveCard($raw);
    }



    protected function defaultSettings(string $settingsField): array
    {
        if (str_contains($settingsField, 'hero')) {
            return [
                ...HeroHeight::defaults(),
                'effect'          => 'fade',
                'autoplay'        => true,
                'autoplay_delay'  => 4000,
                'show_navigation' => true,
                'hide_navigation_mobile' => false,
                'show_pagination' => true,
                'hide_pagination_mobile' => false,
            ];
        }

        if (str_contains($settingsField, 'thumbs')) {
            return [
                ...HeroHeight::defaults(70),
                'thumbs_per_view' => 4,
                'thumbs_per_view_tablet' => 4,
                'thumbs_per_view_mobile' => 4,
                'show_navigation' => true,
                'hide_navigation_mobile' => false,
                'autoplay'        => false,
                'autoplay_delay'  => 4000,
                'openInLightbox'  => false,
                'borderRadius'    => RadiusOptions::THEME_DEFAULT,
            ];
        }

        if (str_contains($settingsField, 'parallax')) {
            return [
                ...HeroHeight::defaults(70),
                'show_navigation' => true,
                'hide_navigation_mobile' => false,
                'show_pagination' => true,
                'hide_pagination_mobile' => false,
                'autoplay'        => false,
                'autoplay_delay'  => 4000,
            ];
        }

        return [
            'slides_per_view' => 3,
            'slides_per_view_tablet' => 0,
            'slides_per_view_mobile' => 0,
            'loop'            => true,
            'autoplay'        => false,
            'autoplay_delay'  => 4000,
            'show_navigation' => true,
            'hide_navigation_mobile' => false,
            'show_pagination' => true,
            'hide_pagination_mobile' => false,
        ] + FontWeightOptions::CARD_DEFAULTS;
    }

    protected function postProcessRows(array $rows, array $settings): array
    {
        foreach ($rows as &$row) {
            // No per-item title alignment: 'auto' keeps the card's alignment.
            $row['eyebrowAlignClass'] = $this->resolveAlignClass($row['subtitle_eyebrow_position'] ?? null);
        }
        unset($row);

        return $rows;
    }
}
