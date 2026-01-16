<?php

declare(strict_types=1);

namespace ShadowCastiel\PHPStan\TestsCheck\Attribute;

use Attribute;

/**
 * Attribute to mark a method as not requiring a test.
 *
 * @psalm-api
 *
 * @param string $description Explanation why this method does not require a test (required, non-empty)
 */
#[Attribute(Attribute::TARGET_METHOD)]
class NoTest
{
    public function __construct(
        public readonly string $description,
    ) {}
}
