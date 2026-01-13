<?php

namespace Tests\Fixtures;

use ShadowCastiel\PHPStan\TestsCheck\Attribute\Behaviour;

class ConcatPathService
{
    #[Behaviour('features/' . 'user_creation.feature')]
    public function concatPath(): void
    {
    }
}
