<?php

namespace App\Exceptions;

use RuntimeException;

class ServiceException extends RuntimeException
{
    public function __construct(
        string $key,
        public readonly array $context = [],
    ) {
        parent::__construct($key);
    }
}
