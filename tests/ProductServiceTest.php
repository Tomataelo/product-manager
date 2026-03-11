<?php

namespace App\Tests;

use App\Dto\ProductDto;
use App\Dto\UpdateProductDto;
use App\Entity\Product;
use App\Enum\Currency;
use App\Enum\Status;
use App\Event\ProductPriceChangedEvent;
use App\Exception\ProductAlreadyExistsException;
use App\Exception\ProductConflictException;
use App\Exception\ProductNotFoundException;
use App\Repository\ProductRepository;
use App\Service\PaginatorService;
use App\Service\PriceHistoryService;
use App\Service\ProductService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\OptimisticLockException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ProductServiceTest extends TestCase
{
    private ProductRepository|MockObject $productRepository;
    private EventDispatcherInterface|MockObject $eventDispatcher;
    private ProductService $productService;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepository::class);
        $priceHistoryService = $this->createMock(PriceHistoryService::class);
        $paginatorService = $this->createMock(PaginatorService::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->productService = new ProductService(
            $this->productRepository,
            $priceHistoryService,
            $paginatorService,
            $this->eventDispatcher,
        );
    }

    public function testCreateProductSuccess(): void
    {
        $dto = $this->createProductDto();

        $this->productRepository
            ->expects($this->once())
            ->method('save');

        $product = $this->productService->createProduct($dto);

        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals('SKU-001', $product->getSku());
        $this->assertEquals('99.99', $product->getPrice());
        $this->assertEquals(Currency::PLN, $product->getCurrency());
        $this->assertEquals(Status::ACTIVE, $product->getStatus());
    }

    public function testCreateProductThrowsExceptionWhenSkuAlreadyExists(): void
    {
        $dto = $this->createProductDto();

        $this->productRepository
            ->method('save')
            ->willThrowException($this->createMock(UniqueConstraintViolationException::class));

        $this->expectException(ProductAlreadyExistsException::class);

        $this->productService->createProduct($dto);
    }

    public function testGetProductThrowsExceptionWhenNotFound(): void
    {
        $this->productRepository
            ->method('find')
            ->willReturn(null);

        $this->expectException(ProductNotFoundException::class);

        $this->productService->getProduct(999);
    }

    public function testGetProductSuccess(): void
    {
        $product = $this->createProduct();

        $this->productRepository
            ->method('find')
            ->willReturn($product);

        $result = $this->productService->getProduct(1);

        $this->assertEquals($product, $result);
    }

    public function testUpdateProductSuccess(): void
    {
        $product = $this->createProduct();

        $this->productRepository
            ->method('find')
            ->willReturn($product);

        $dto = new UpdateProductDto();
        $dto->setName('Updated Name');
        $dto->setVersion(1);

        $result = $this->productService->updateProduct(1, $dto);

        $this->assertEquals('Updated Name', $result->getName());
    }

    public function testUpdateProductThrowsExceptionWhenNotFound(): void
    {
        $this->productRepository
            ->method('find')
            ->willReturn(null);

        $dto = new UpdateProductDto();
        $dto->setVersion(1);

        $this->expectException(ProductNotFoundException::class);

        $this->productService->updateProduct(999, $dto);
    }

    public function testUpdateProductThrowsConflictException(): void
    {
        $product = $this->createProduct();

        $this->productRepository
            ->method('find')
            ->willReturn($product);

        $this->productRepository
            ->method('lockProduct')
            ->willThrowException($this->createMock(OptimisticLockException::class));

        $dto = new UpdateProductDto();
        $dto->setVersion(1);

        $this->expectException(ProductConflictException::class);

        $this->productService->updateProduct(1, $dto);
    }

    public function testUpdateProductDispatchesEventOnPriceChange(): void
    {
        $product = $this->createProduct();

        $this->productRepository
            ->method('find')
            ->willReturn($product);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(ProductPriceChangedEvent::class));

        $dto = new UpdateProductDto();
        $dto->setPrice(999.99);
        $dto->setVersion(1);

        $this->productService->updateProduct(1, $dto);
    }

    public function testDeleteProductSuccess(): void
    {
        $product = $this->createProduct();

        $this->productRepository
            ->method('find')
            ->willReturn($product);

        $this->productRepository
            ->expects($this->once())
            ->method('save');

        $this->productService->deleteProduct(1);

        $this->assertTrue($product->isDeleted());
    }

    public function testDeleteProductThrowsExceptionWhenNotFound(): void
    {
        $this->productRepository
            ->method('find')
            ->willReturn(null);

        $this->expectException(ProductNotFoundException::class);

        $this->productService->deleteProduct(999);
    }




    private function createProductDto(): ProductDto
    {
        $dto = new ProductDto();
        $dto->setName('Test Product');
        $dto->setSku('SKU-001');
        $dto->setPrice('99.99');
        $dto->setCurrency('PLN');
        $dto->setStatus('active');

        return $dto;
    }

    private function createProduct(): Product
    {
        $product = new Product();
        $product->setName('Test Product');
        $product->setSku('SKU-001');
        $product->setPrice('99.99');
        $product->setCurrency(Currency::PLN);
        $product->setStatus(Status::ACTIVE);

        return $product;
    }
}
