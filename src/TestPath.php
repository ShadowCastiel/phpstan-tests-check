<?php

declare(strict_types=1);

namespace ShadowCastiel\PHPStan\TestsCheck;

/**
 * Helper class for building test file paths.
 *
 * @psalm-api
 */
class TestPath
{
    public static function root(string $path): string
    {
        return '@root/' . ltrim($path, '/');
    }

    public static function relative(string $path): string
    {
        return $path;
    }

    public static function absolute(string $path): string
    {
        return $path;
    }
}
