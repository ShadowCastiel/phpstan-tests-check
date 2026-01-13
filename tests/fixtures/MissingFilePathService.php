<?php

namespace Tests\Fixtures;

use ShadowCastiel\PHPStan\TestsCheck\Attribute\Behaviour;
use ShadowCastiel\PHPStan\TestsCheck\Attribute\Unit;

class MissingFilePathService
{
    #[Behaviour]
    public function createUser(): void
    {
    }

    #[Unit]
    public function validateEmail(): bool
    {
        return true;
    }
}

