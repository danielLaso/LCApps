<?php

namespace App\Orders\Domain;

use App\Orders\Domain\Order;
use App\Orders\Domain\OrderLine;
use App\Catalog\Domain\Product;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testConfirmOrderWithSufficientStock(): void
    {
        $product = new Product('Product 1', 10.0, 5); // stock 5

        $order = new Order('REF-123');
        $orderLine = new OrderLine($product, 3); // pide 3
        $order->addLine($orderLine);

        $order->confirm();

        $this->assertEquals('confirmed', $order->getStatus());
        $this->assertEquals(2, $product->getAvailableStock()); // stock actualizado
    }

    public function testConfirmOrderWithInsufficientStock(): void
    {
        $this->expectException(\DomainException::class);

        $product = new Product('Product 1', 10.0, 2); // stock 2

        $order = new Order('REF-124');
        $orderLine = new OrderLine($product, 5); // pide 5 → error
        $order->addLine($orderLine);

        $order->confirm();
    }
}
