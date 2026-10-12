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

namespace Connetation\T3wtkElements\Utility;

use Connetation\T3wtkBase\Utility\ResponsiveValue;

/**
 * Hero slider height (settings.height, renderType connResponsiveRange) →
 * one vh value per device. Shared by every processor rendering a hero slider.
 *
 * Stored as "desktop,tablet,mobile", e.g. "70,,45": an empty or invalid
 * tablet/mobile value inherits from the next larger device; a single value
 * is used for all devices. Former keyword values (medium/large/fullscreen,
 * from the old select) map to their vh equivalents.
 */
final class HeroHeight
{
    public const DEFAULT = 50;

    public const LEGACY = ['medium' => 55, 'large' => 70, 'fullscreen' => 100];

    /** @return array{height: int, heightTablet: int, heightMobile: int} */
    public static function resolve(mixed $raw, int $default = self::DEFAULT): array
    {
        $values = ResponsiveValue::inherited(ResponsiveValue::parse($raw, 20, 100, $default, self::LEGACY));

        return ['height' => $values['desktop'], 'heightTablet' => $values['tablet'], 'heightMobile' => $values['mobile']];
    }

    /** @return array{height: int, heightTablet: int, heightMobile: int} */
    public static function defaults(int $default = self::DEFAULT): array
    {
        return ['height' => $default, 'heightTablet' => $default, 'heightMobile' => $default];
    }
}
