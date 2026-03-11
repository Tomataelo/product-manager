<?php

namespace App\Controller;

use App\Dto\ProductDto;
use App\Dto\UpdateProductDto;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/product')]
class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductService $productService,
    ) {}

    #[Route('', methods: ['POST'])]
    public function createProduct(#[MapRequestPayload] ProductDto $productDto): JsonResponse
    {
        $createdProduct = $this->productService->createProduct($productDto);
        return $this->json($createdProduct, 201, context: ['groups' => ['product:write']]);
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function updateProduct(int $id, #[MapRequestPayload] UpdateProductDto $productDto): JsonResponse
    {
        $updatedProduct = $this->productService->updateProduct($id, $productDto);
        return $this->json($updatedProduct, context: ['groups' => ['product:write']]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getProduct(int $id): JsonResponse
    {
        $product = $this->productService->getProduct($id);
        return $this->json($product, context: ['groups' => ['product:read', 'product:write']]);
    }

    #[Route('', methods: ['GET'])]
    public function getAllProducts(Request $request): JsonResponse
    {
        $status = $request->query->get('status');
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 5);

        $products = $this->productService->getAllProducts($status, $page, $limit);
        return $this->json($products, context: ['groups' => ['product:read', 'product:write']]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteProduct(int $id): JsonResponse
    {
        $this->productService->deleteProduct($id);
        return $this->json(1);
    }
}
