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

    private const MIN = 20;
    private const MAX = 100;
    private const LEGACY = ['medium' => 55, 'large' => 70, 'fullscreen' => 100];

    /** @return array{height: int, heightTablet: int, heightMobile: int} */
    public static function resolve(mixed $raw): array
    {
        $parts   = \is_string($raw) ? array_map('trim', explode(',', $raw)) : [$raw];
        $desktop = self::single($parts[0] ?? null) ?? self::DEFAULT;
        $tablet  = self::single($parts[1] ?? null) ?? $desktop;
        $mobile  = self::single($parts[2] ?? null) ?? $tablet;

        return ['height' => $desktop, 'heightTablet' => $tablet, 'heightMobile' => $mobile];
    }

    /** @return array{height: int, heightTablet: int, heightMobile: int} */
    public static function defaults(): array
    {
        return ['height' => self::DEFAULT, 'heightTablet' => self::DEFAULT, 'heightMobile' => self::DEFAULT];
    }

    /** A valid vh value, or null when unset/invalid. */
    private static function single(mixed $value): ?int
    {
        if (\is_string($value) && isset(self::LEGACY[$value])) {
            return self::LEGACY[$value];
        }
        if ($value === null || $value === '' || !is_numeric($value)) {
            return null;
        }
        $height = (int)$value;

        return ($height >= self::MIN && $height <= self::MAX) ? $height : null;
    }
}
