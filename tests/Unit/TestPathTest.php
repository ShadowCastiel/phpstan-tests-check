<?php

declare(strict_types=1);

namespace ShadowCastiel\PHPStan\TestAttributes\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ShadowCastiel\PHPStan\TestAttributes\TestPath;

class TestPathTest extends TestCase
{
    public function testRootAddsRootPrefix(): void
    {
        $result = TestPath::root('tests/Unit/Test.php');
        $this->assertEquals('@root/tests/Unit/Test.php', $result);
    }

    public function testRootTrimsLeadingSlash(): void
    {
        $result = TestPath::root('/tests/Unit/Test.php');
        $this->assertEquals('@root/tests/Unit/Test.php', $result);
    }

    public function testRelativeReturnsAsIs(): void
    {
        $path = 'tests/Unit/Test.php';
        $result = TestPath::relative($path);
        $this->assertEquals($path, $result);
    }

    public function testAbsoluteReturnsAsIs(): void
    {
        $path = '/absolute/path/to/Test.php';
        $result = TestPath::absolute($path);
        $this->assertEquals($path, $result);
    }
}
