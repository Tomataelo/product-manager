<?php

namespace App\Exception;

class ProductNotFoundException extends \RuntimeException implements HttpExceptionInterface
{
    public function __construct(int $id)
    {
        parent::__construct("Product with ID: {$id} not found.");
    }

    public function getStatusCode(): int
    {
        return 404;
    }
}
