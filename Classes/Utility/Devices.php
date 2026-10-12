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

use Connetation\T3wtkBase\Utility\Devices as BaseDevices;

/**
 * @deprecated Moved to EXT:conn_t3wtk_base — use Connetation\T3wtkBase\Utility\Devices.
 *             Kept as a thin alias for existing code.
 */
final class Devices
{
    public const ALL           = BaseDevices::ALL;
    public const TABLET_MIN    = BaseDevices::TABLET_MIN;
    public const DESKTOP_MIN   = BaseDevices::DESKTOP_MIN;
    public const TABLET_INFIX  = BaseDevices::TABLET_INFIX;
    public const DESKTOP_INFIX = BaseDevices::DESKTOP_INFIX;

    /** @return array{min: ?int, max: ?int} */
    public static function range(string $device): array
    {
        return BaseDevices::range($device);
    }
}
