<?php

namespace App\EventListener;

use App\Event\ProductPriceChangedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: ProductPriceChangedEvent::class)]
readonly class ProductPriceChangedListener
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function __invoke(ProductPriceChangedEvent $event): void
    {
        $this->logger->info('Product price changed', [
            'product_id' => $event->product->getId(),
            'product_name' => $event->product->getName(),
            'old_price' => $event->oldPrice,
            'new_price' => $event->newPrice,
        ]);
    }
}
