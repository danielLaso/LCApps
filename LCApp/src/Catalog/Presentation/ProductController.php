<?php

namespace App\Catalog\Presentation;

use App\Catalog\Domain\Product;
use App\Catalog\Domain\ValueObject\Money;
use App\Catalog\Infrastructure\DoctrineProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/products')]
class ProductController extends AbstractController
{
    #[Route('', methods: ['POST'])]
    public function create(Request $request, DoctrineProductRepository $productRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product(
            $data['name'],
            new Money((float) $data['price']),
            (int) $data['availableStock']
        );

        $productRepo->save($product);

        return new JsonResponse(['id' => $product->getId()], JsonResponse::HTTP_CREATED);
    }

    #[Route('', methods: ['GET'])]
    public function list(DoctrineProductRepository $productRepo): JsonResponse
    {
        $products = $productRepo->findAll();

        $data = array_map(function (Product $product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice()->getAmount(),
                'availableStock' => $product->getAvailableStock(),
            ];
        }, $products);

        return new JsonResponse($data);
    }

    #[Route('/{id}/stock', methods: ['PATCH'])]
    public function updateStock(int $id, Request $request, DoctrineProductRepository $productRepo): JsonResponse
    {
        $product = $productRepo->find($id);

        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $product->setAvailableStock((int) $data['availableStock']);

        $productRepo->save($product);

        return new JsonResponse(['message' => 'Stock updated']);
    }
}
