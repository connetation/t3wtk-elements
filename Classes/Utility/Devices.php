<?php

declare(strict_types=1);

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
