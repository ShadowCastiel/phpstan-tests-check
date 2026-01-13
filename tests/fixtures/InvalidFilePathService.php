<?php

namespace Tests\Fixtures;

use ShadowCastiel\PHPStan\TestsCheck\Attribute\Behaviour;
use ShadowCastiel\PHPStan\TestsCheck\Attribute\Unit;

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

