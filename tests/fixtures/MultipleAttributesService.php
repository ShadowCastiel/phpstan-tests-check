<?php

namespace Tests\Fixtures;

use ShadowCastiel\PHPStan\TestsCheck\Attribute\Behaviour;
use ShadowCastiel\PHPStan\TestsCheck\Attribute\NoTest;

class MultipleAttributesService
{
    #[NoTest]
    #[Behaviour('features/user_creation.feature')]
    public function multipleAttributes(): void
    {
    }
}
