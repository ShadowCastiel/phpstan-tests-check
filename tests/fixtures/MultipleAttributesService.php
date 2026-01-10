<?php

namespace Tests\Fixtures;

use ShadowCastiel\PHPStan\TestAttributes\Attribute\Behaviour;
use ShadowCastiel\PHPStan\TestAttributes\Attribute\NoTest;

class MultipleAttributesService
{
    #[NoTest]
    #[Behaviour('features/user_creation.feature')]
    public function multipleAttributes(): void
    {
    }
}
