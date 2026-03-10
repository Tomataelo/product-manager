<?php

namespace App\Exception;

class ProductAlreadyExistsException extends \RuntimeException implements HttpExceptionInterface
{
    public function __construct(string $sku)
    {
        parent::__construct("Product with SKU: '{$sku}' already exists.");
    }

    public function getStatusCode(): int
    {
        return 409;
    }
}
