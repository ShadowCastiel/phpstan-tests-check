<?php

namespace Tests\Fixtures;

use ShadowCastiel\PHPStan\TestAttributes\Attribute\Behaviour;
use ShadowCastiel\PHPStan\TestAttributes\Attribute\Unit;

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

