<?php

declare(strict_types=1);

namespace App\Traits;

trait EnumToArray
{
    /** @return array<int, array<string, int>> */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /** @return array<int, array<string, int>> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** @return array<int, array<string, int>> */
    public static function array(): array
    {
        return array_combine(self::values(), self::names());
    }
}
