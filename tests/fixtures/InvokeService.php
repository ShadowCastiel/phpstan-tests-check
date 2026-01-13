<?php

namespace Tests\Fixtures;

use ShadowCastiel\PHPStan\TestsCheck\Attribute\Behaviour;

class InvokeService
{
    #[Behaviour('features/invoke.feature')]
    public function __invoke(): void
    {
    }

    public function __construct()
    {
    }
}

