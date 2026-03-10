<?php

namespace App\Service;

use App\Dto\ProductDto;
use App\Dto\UpdateProductDto;
use App\Entity\Product;
use App\Enum\Currency;
use App\Enum\Status;
use App\Event\ProductPriceChangedEvent;
use App\Exception\ProductAlreadyExistsException;
use App\Exception\ProductNotFoundException;
use App\Repository\ProductRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class ProductService
{
    public function __construct(
        private ProductRepository $productRepository,
        private PriceHistoryService $priceHistoryService,
        private PaginatorService $paginatorService,
        private EventDispatcherInterface $eventDispatcher,
    ){}

    public function createProduct(ProductDTO $productDTO): Product
    {
        $product = new Product();
        $product->setName($productDTO->getName());
        $product->setSku($productDTO->getSku());
        $product->setPrice($productDTO->getPrice());
        $product->setCurrency(Currency::from($productDTO->getCurrency()));
        $product->setStatus(Status::from($productDTO->getStatus()));

        try {
            $this->productRepository->save($product);
        } catch (UniqueConstraintViolationException) {
            throw new ProductAlreadyExistsException($productDTO->getSku());
        }

        return $product;
    }

    public function updateProduct(int $id, UpdateProductDto $updateProductDto): Product
    {
        $product = $this->getProduct($id);

        if ($updateProductDto->getName() !== null && $updateProductDto->getName() !== $product->getName()) {
            $product->setName($updateProductDto->getName());
        }
        if ($updateProductDto->getSku() !== null && $updateProductDto->getSku() !== $product->getSku()) {
            $product->setSku($updateProductDto->getSku());
        }

        if ($updateProductDto->getPrice() !== null && $updateProductDto->getPrice() != $product->getPrice()) {
            $oldPrice = $product->getPrice();
            $product->setPrice($updateProductDto->getPrice());

            $this->priceHistoryService->priceChanged($oldPrice, $product);

            $this->eventDispatcher->dispatch(
                new ProductPriceChangedEvent($product, $oldPrice, $updateProductDto->getPrice())
            );
        }

        if ($updateProductDto->getCurrency() !== null && $updateProductDto->getCurrency() !== $product->getCurrency()) {
            $product->setCurrency(Currency::from($updateProductDto->getCurrency()));
        }
        if ($updateProductDto->getStatus() !== null && $updateProductDto->getStatus() !== $product->getStatus()) {
            $product->setStatus(Status::from($updateProductDto->getStatus()));
        }

        $this->productRepository->save($product);

        return $product;
    }

    public function deleteProduct(int $id): void
    {
        $product = $this->getProduct($id);
        $product->setIsDeleted(true);
        $this->productRepository->save($product);
    }

    public function getNotDelProduct(int $id): Product
    {
        return $this->productRepository->findOneBy(['is_deleted' => false])
            ?? throw new ProductNotFoundException($id);
    }

    public function getProduct(int $id): Product
    {
        return $this->productRepository->find($id)
            ?? throw new ProductNotFoundException($id);
    }

    public function getAllProducts(?string $status, int $page, int $limit): array
    {
        $qb = $this->productRepository->findByFilterQuery($status);
        return $this->paginatorService->paginate($qb, $page, $limit);
    }
}
