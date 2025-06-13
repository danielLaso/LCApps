<?php

namespace App\Catalog\Presentation;

use App\Catalog\Domain\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/products')]
class ProductController extends AbstractController
{
    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product(
            $data['name'],
            (float) $data['price'],
            (int) $data['availableStock']
        );

        $em->persist($product);
        $em->flush();

        return new JsonResponse(['id' => $product->getId()], JsonResponse::HTTP_CREATED);
    }

    #[Route('', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $products = $em->getRepository(Product::class)->findAll();

        $data = array_map(function (Product $product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'availableStock' => $product->getAvailableStock(),
            ];
        }, $products);

        return new JsonResponse($data);
    }

    #[Route('/{id}/stock', methods: ['PATCH'])]
    public function updateStock(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $product->setAvailableStock((int) $data['availableStock']);

        $em->flush();

        return new JsonResponse(['message' => 'Stock updated']);
    }
}
