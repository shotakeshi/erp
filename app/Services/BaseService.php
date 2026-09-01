<?php

namespace App\Services;

use App\Exceptions\ServiceException;

abstract class BaseService
{
    protected function fail(string $message, array $context = []): void
    {
        throw new ServiceException($message, $context);
    }
}