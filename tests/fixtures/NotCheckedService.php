<?php

namespace Tests\Fixtures;

class NotCheckedService
{
    public function createUser(): void
    {
    }

    public function validateEmail(): bool
    {
        return true;
    }
}

