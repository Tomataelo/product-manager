<?php

namespace App\Exception;

use App\Exception\HttpExceptionInterface;

class ProductConflictException extends \RuntimeException implements HttpExceptionInterface
{
    public function __construct()
    {
        parent::__construct('Product version mismatch. Please fetch the latest product version and try again.');
    }

    public function getStatusCode(): int
    {
        return 409;
    }
}
