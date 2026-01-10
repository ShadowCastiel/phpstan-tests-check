<?php

namespace Tests\Fixtures;

use ShadowCastiel\PHPStan\TestAttributes\Attribute\NoTest;

class ExcludedMethodsService
{
    #[NoTest]
    public function publicMethod(): void
    {
    }

    public function __construct()
    {
    }

    public function __toString(): string
    {
        return '';
    }
}
