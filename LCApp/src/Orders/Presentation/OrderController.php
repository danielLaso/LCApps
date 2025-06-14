<?php

namespace App\Orders\Presentation;

use App\Catalog\Domain\Product;
use App\Catalog\Infrastructure\DoctrineProductRepository;
use App\Orders\Domain\Order;
use App\Orders\Domain\OrderLine;
use App\Orders\Infrastructure\DoctrineOrderRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/orders')]
class OrderController extends AbstractController
{
    #[Route('', methods: ['POST'])]
    public function create(
        Request $request,
        DoctrineOrderRepository $orderRepo,
        DoctrineProductRepository $productRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $order = new Order($data['reference']);
        foreach ($data['lines'] as $lineData) {
            $product = $productRepo->find($lineData['productId']);
            if (!$product) {
                return new JsonResponse(['error' => 'Product not found (id '.$lineData['productId'].')'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $orderLine = new OrderLine($product, (int) $lineData['quantity'], $order);
            $order->addLine($orderLine);
        }

        $orderRepo->save($order);

        return new JsonResponse(['id' => $order->getId()], JsonResponse::HTTP_CREATED);
    }

    #[Route('', methods: ['GET'])]
    public function list(DoctrineOrderRepository $orderRepo): JsonResponse
    {
        $orders = $orderRepo->findAll();

        $data = array_map(function (Order $order) {
            return [
                'id' => $order->getId(),
                'reference' => $order->getReference(),
                'status' => $order->getStatus(),
                'lines' => $order->getLines()->map(function (OrderLine $line) {
                    return [
                        'productId' => $line->getProduct()->getId(),
                        'productName' => $line->getProduct()->getName(),
                        'quantity' => $line->getQuantity(),
                    ];
                })->toArray(),
            ];
        }, $orders);

        return new JsonResponse($data);
    }

    #[Route('/{id}/confirm', methods: ['PATCH'])]
    public function confirm(
        int $id,
        DoctrineOrderRepository $orderRepo,
        DoctrineProductRepository $productRepo
    ): JsonResponse {
        $order = $orderRepo->find($id);

        if (!$order) {
            return new JsonResponse(['error' => 'Order not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        foreach ($order->getLines() as $line) {
            $product = $line->getProduct();
            $quantity = $line->getQuantity();

            if ($quantity > $product->getAvailableStock()) {
                return new JsonResponse([
                    'error' => 'Not enough stock for product '.$product->getName()
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        foreach ($order->getLines() as $line) {
            $product = $line->getProduct();
            $product->decreaseStock($line->getQuantity());
            $productRepo->save($product);
        }

        $order->confirm();
        $orderRepo->save($order);

        return new JsonResponse(['message' => 'Order confirmed']);
    }
}
