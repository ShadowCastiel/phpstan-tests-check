<?php

declare(strict_types=1);

namespace ShadowCastiel\PHPStan\TestsCheck\Attribute;

use Attribute;

/**
 * Attribute to mark a method as having a unit test.
 *
 * @psalm-api
 *
 * @param string $filePath Path to the PHPUnit test file (relative, absolute, @root/ prefix, or class-string)
 *
 * @phpstan-param string|class-string $filePath
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Unit
{
    public function __construct(
        public readonly string $filePath,
    ) {}
}
