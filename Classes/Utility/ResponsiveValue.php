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

namespace Connetation\T3wtkElements\Utility;

use Connetation\T3wtkBase\Utility\ResponsiveValue as BaseResponsiveValue;

/**
 * @deprecated Moved to EXT:conn_t3wtk_base — use Connetation\T3wtkBase\Utility\ResponsiveValue.
 *             Kept as a thin alias for existing code.
 */
final class ResponsiveValue
{
    /** @return array{desktop: int, tablet: ?int, mobile: ?int} */
    public static function parse(mixed $raw, int $min, int $max, int $default, array $legacy = []): array
    {
        return BaseResponsiveValue::parse($raw, $min, $max, $default, $legacy);
    }

    /** @return array{desktop: int, tablet: int, mobile: int} */
    public static function inherited(array $values): array
    {
        return BaseResponsiveValue::inherited($values);
    }

    public static function rowColsClass(array $values, ?int $autoTablet = null, ?int $autoMobile = null): string
    {
        return BaseResponsiveValue::rowColsClass($values, $autoTablet, $autoMobile);
    }
}
