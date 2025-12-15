<?php

namespace App\Http\Controllers\API\V1\Health\Checkers;

interface HealthCheckerInterface
{
    public function check(): array;

    public function getName(): string;
}
