<?php

namespace Tests\Fixtures;

use ShadowCastiel\PHPStan\TestsCheck\Attribute\NoTest;

class ShortNameService
{
    #[NoTest]
    public function doSomething(): void
    {
    }

    public function missingAttribute(): void
    {
    }
}
