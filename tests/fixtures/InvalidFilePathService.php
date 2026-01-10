<?php

namespace Tests\Fixtures;

use ShadowCastiel\PHPStan\TestAttributes\Attribute\Behaviour;
use ShadowCastiel\PHPStan\TestAttributes\Attribute\Unit;

class InvalidFilePathService
{
    #[Behaviour('features/non_existent.feature')]
    public function createUser(): void
    {
    }

    #[Unit('tests/Unit/NonExistentTest.php')]
    public function validateEmail(): bool
    {
        return true;
    }
}

