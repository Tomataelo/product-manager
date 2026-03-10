<?php

namespace App\Event;

use App\Entity\Product;

final readonly class ProductPriceChangedEvent
{
    public function __construct(
        public Product $product,
        public string  $oldPrice,
        public string  $newPrice,
    ) {}
}
