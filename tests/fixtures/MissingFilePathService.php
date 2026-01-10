<?php

namespace Tests\Fixtures;

use ShadowCastiel\PHPStan\TestAttributes\Attribute\Behaviour;
use ShadowCastiel\PHPStan\TestAttributes\Attribute\Unit;

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

