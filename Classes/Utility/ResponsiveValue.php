<?php

declare(strict_types=1);

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
