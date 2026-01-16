<?php

namespace Tests\Fixtures;

use ShadowCastiel\PHPStan\TestsCheck\Attribute\Unit;
use Tests\Unit\ValidServiceTest;

class ClassStringService
{
    #[Unit(ValidServiceTest::class)]
    public function processData(): void
    {
    }
}
