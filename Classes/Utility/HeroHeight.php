<?php

declare(strict_types=1);

namespace Connetation\T3wtkElements\Utility;

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
