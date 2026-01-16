<?php

namespace Tests\Fixtures;

use ShadowCastiel\PHPStan\TestsCheck\Attribute\NoTest;

class ShortNameService
{
    #[NoTest('Simple method without business logic')]
    public function doSomething(): void
    {
    }

    public function missingAttribute(): void
    {
    }
}
