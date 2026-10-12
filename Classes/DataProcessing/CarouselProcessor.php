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

use Connetation\T3wtkBase\Utility\RadiusOptions;
use Connetation\T3wtkBase\DataProcessing\AbstractItemsProcessor;

final class CarouselProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_t3wtk_elements_domain_model_carousel_slide';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3wtk_elements_carousel_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $allowedRatios = ['21x9', '16x9', '4x3', '1x1', 'original'];
        $aspectRatio   = in_array($raw['aspectRatio'] ?? '', $allowedRatios, true)
            ? $raw['aspectRatio'] : '16x9';

        $allowedOriginalHeights = ['auto', '55vh', '70vh', '100vh'];
        $originalHeightRaw = $raw['originalHeight'] ?? 'auto';
        $originalHeight = in_array($originalHeightRaw, $allowedOriginalHeights, true)
            ? $originalHeightRaw : 'auto';

        $allowedStyles = ['none', 'shadow', 'border'];
        $styleRaw = $raw['style'] ?? 'none';
        $style = in_array($styleRaw, $allowedStyles, true) ? $styleRaw : 'none';

        // Output as data-radius; sizes come from connetation/t3wtk-base's conn-radius-option() mixin.
        $borderRadius = RadiusOptions::resolve($raw['borderRadius'] ?? null);

        return [
            'aspectRatio'       => $aspectRatio,
            'originalHeight'    => $originalHeight,
            'style'             => $style,
            'borderRadius'      => $borderRadius,
            'controls'          => (bool)(int)($raw['controls']   ?? 1),
            'indicators'        => (bool)(int)($raw['indicators'] ?? 1),
            'autoplay'          => (bool)(int)($raw['autoplay']   ?? 0),
            'interval'          => max(1000, (int)($raw['interval'] ?? 5000)),
            'fade'              => (bool)(int)($raw['fade']       ?? 0),
            'dark'              => (bool)(int)($raw['dark']       ?? 0),
            'openInLightbox'    => (bool)(int)($raw['openInLightbox'] ?? 0),
            'captionTitleColor' => $this->sanitizeHexColor($raw['captionTitleColor'] ?? '', '#ffffff'),
            'captionTextColor'  => $this->sanitizeHexColor($raw['captionTextColor'] ?? '', '#ffffff'),
            'captionBgColor'    => $this->sanitizeHexColor($raw['captionBgColor'] ?? '', '#00000094'),
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'aspectRatio'       => '16x9',
            'originalHeight'    => 'auto',
            'style'             => 'none',
            'borderRadius'      => RadiusOptions::THEME_DEFAULT,
            'controls'          => true,
            'indicators'        => true,
            'autoplay'          => false,
            'interval'          => 5000,
            'fade'              => false,
            'dark'              => false,
            'openInLightbox'    => false,
            'captionTitleColor' => '#ffffff',
            'captionTextColor'  => '#ffffff',
            'captionBgColor'    => '#00000094',
        ];
    }
}
