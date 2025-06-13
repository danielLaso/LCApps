<?php

namespace App\Orders\Presentation;

use App\Catalog\Domain\Product;
use App\Orders\Domain\Order;
use App\Orders\Domain\OrderLine;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/orders')]
class OrderController extends AbstractController
{
    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $order = new Order($data['reference']);

        foreach ($data['lines'] as $lineData) {
            $product = $em->getRepository(Product::class)->find($lineData['productId']);
            if (!$product) {
                return new JsonResponse(['error' => 'Product not found (id '.$lineData['productId'].')'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $orderLine = new OrderLine($product, (int) $lineData['quantity']);
            $order->addLine($orderLine);
        }

        $em->persist($order);
        $em->flush();

        return new JsonResponse(['id' => $order->getId()], JsonResponse::HTTP_CREATED);
    }

    #[Route('', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $orders = $em->getRepository(Order::class)->findAll();

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
    public function confirm(int $id, EntityManagerInterface $em): JsonResponse
    {
        $order = $em->getRepository(Order::class)->find($id);

        if (!$order) {
            return new JsonResponse(['error' => 'Order not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Validar y actualizar stock
        foreach ($order->getLines() as $line) {
            $product = $line->getProduct();
            $quantity = $line->getQuantity();

            if ($quantity > $product->getAvailableStock()) {
                return new JsonResponse([
                    'error' => 'Not enough stock for product '.$product->getName()
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        // Disminuir stock de productos
        foreach ($order->getLines() as $line) {
            $product = $line->getProduct();
            $product->setAvailableStock($product->getAvailableStock() - $line->getQuantity());
        }

        // Cambiar estado del pedido
        $order->confirm();

        $em->flush();

        return new JsonResponse(['message' => 'Order confirmed']);
    }
}
