<?php

declare(strict_types=1);

namespace ShadowCastiel\PHPStan\TestsCheck\Attribute;

use Attribute;

/**
 * Attribute to mark a method as having a behaviour test.
 *
 * @psalm-api
 *
 * @param string $filePath Path to the feature file (relative, absolute, or @root/ prefix)
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Behaviour
{
    public function __construct(
        public readonly string $filePath,
    ) {}
}
