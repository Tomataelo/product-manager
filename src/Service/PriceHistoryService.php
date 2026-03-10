<?php

namespace App\Service;

use App\Entity\PriceHistory;
use App\Entity\Product;
use App\Repository\PriceHistoryRepository;

readonly class PriceHistoryService
{
    public function __construct(
        private PriceHistoryRepository $priceHistoryRepository,
    ){}

    public function priceChanged(string $oldPrice, Product $product): void
    {
        $newPriceHistory = new PriceHistory();
        $newPriceHistory->setProductId($product);
        $newPriceHistory->setOldPrice($oldPrice);
        $newPriceHistory->setNewPrice($product->getPrice());

        $this->priceHistoryRepository->save($newPriceHistory);
    }
}
