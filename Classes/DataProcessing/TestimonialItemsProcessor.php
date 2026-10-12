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
use Connetation\T3wtkBase\Utility\ResponsiveValue;

final class TestimonialItemsProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_t3wtk_elements_domain_model_testimonial_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'testimonials';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3wtk_elements_testimonials_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        // Per device ("desktop,tablet,mobile"); empty tablet/mobile (0) keeps
        // the element's automatic layout for that screen size.
        $columnsByDevice = ResponsiveValue::parse($raw['columns'] ?? null, 1, 4, 3);
        $columns         = $columnsByDevice['desktop'];
        $colMap  = [
            1 => 'col-12',
            2 => 'col-sm-6',
            3 => 'col-sm-6 col-lg-4',
        ];
        $layout = in_array($raw['layout'] ?? 'grid', ['grid', 'carousel'], true)
            ? ($raw['layout'] ?? 'grid')
            : 'grid';

        return [
            'layout'        => $layout,
            'columns'       => $columns,
            'columnsTablet' => $columnsByDevice['tablet'] ?? 0,
            'columnsMobile' => $columnsByDevice['mobile'] ?? 0,
            'colClass'      => $colMap[$columns] ?? $colMap[3],
            'autoplay'      => (bool)($raw['autoplay'] ?? true),
            'autoplayDelay' => max(1000, (int)($raw['autoplayDelay'] ?? 4000)),
            'showRating'    => (bool)($raw['showRating'] ?? true),
            'accentBar'     => (bool)($raw['accentBar'] ?? true),
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'layout'        => 'grid',
            'columns'       => 3,
            'columnsTablet' => 0,
            'columnsMobile' => 0,
            'colClass'      => 'col-sm-6 col-lg-4',
            'autoplay'      => true,
            'autoplayDelay' => 4000,
            'showRating'    => true,
            'accentBar'     => true,
        ];
    }
}
