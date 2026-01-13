<?php

namespace Tests\Fixtures;

use ShadowCastiel\PHPStan\TestsCheck\Attribute\Behaviour;
use ShadowCastiel\PHPStan\TestsCheck\Attribute\Unit;

class PathFormatsService
{
    #[Behaviour('features/relative.feature')]
    public function relativePath(): void
    {
    }

    #[Unit('@root/tests/Unit/PathFormatsServiceTest.php')]
    public function rootRelativePath(): void
    {
    }
}

