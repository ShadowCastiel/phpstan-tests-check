<?php

namespace Tests\Fixtures;

use ShadowCastiel\PHPStan\TestAttributes\Attribute\Behaviour;
use ShadowCastiel\PHPStan\TestAttributes\Attribute\Unit;
use ShadowCastiel\PHPStan\TestAttributes\Attribute\NoTest;

class ValidService
{
    #[Behaviour('features/user_creation.feature')]
    public function createUser(): void
    {
    }

    #[Unit('tests/Unit/ValidServiceTest.php')]
    public function validateEmail(): bool
    {
        return true;
    }

    #[NoTest]
    public function getConfig(): array
    {
        return [];
    }

    private function privateMethod(): void
    {
    }

    public function __construct()
    {
    }
}

