<?php

namespace Tests\Fixtures;

class MissingAttributeService
{
    public function createUser(): void
    {
    }

    public function validateEmail(): bool
    {
        return true;
    }
}

