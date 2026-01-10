<?php

declare(strict_types=1);

namespace ShadowCastiel\PHPStan\TestAttributes\Attribute;

use Attribute;

/**
 * Attribute to mark a method as not requiring a test.
 *
 * @psalm-api
 */
#[Attribute(Attribute::TARGET_METHOD)]
class NoTest {}
