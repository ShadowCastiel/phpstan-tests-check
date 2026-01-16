<?php

namespace Tests\Fixtures;

use ShadowCastiel\PHPStan\TestsCheck\Attribute\Behaviour;
use ShadowCastiel\PHPStan\TestsCheck\Attribute\Unit;
use ShadowCastiel\PHPStan\TestsCheck\Attribute\NoTest;

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

    #[NoTest('Simple getter that returns configuration array, no business logic to test')]
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

