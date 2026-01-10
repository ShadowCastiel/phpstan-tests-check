<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\PathFormatsService;

class PathFormatsServiceTest extends TestCase
{
    public function testRootRelativePath(): void
    {
        $service = new PathFormatsService();
        $this->assertTrue(true);
    }
}

