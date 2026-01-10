<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\ValidService;

class ValidServiceTest extends TestCase
{
    public function testValidateEmail(): void
    {
        $service = new ValidService();
        $this->assertTrue($service->validateEmail());
    }
}

