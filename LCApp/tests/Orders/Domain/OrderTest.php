<?php

namespace App\Tests\Orders\Domain;

use App\Orders\Domain\Order;
use App\Orders\Domain\OrderLine;
use App\Catalog\Domain\Product;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testOrderIsCreatedWithPendingStatus(): void
    {
        $order = new Order('REF-TEST');

        $this->assertEquals('REF-TEST', $order->getReference());
        $this->assertEquals('pending', $order->getStatus());
        $this->assertCount(0, $order->getLines());
    }

    public function testAddLineAddsOrderLine(): void
    {
        $order = new Order('REF-ADDLINE');
        $product = new Product('Product test', 10.0, 5);

        $orderLine = new OrderLine($product, 2, $order);

        $order->addLine($orderLine);

        $this->assertCount(1, $order->getLines());
        $this->assertSame($order, $orderLine->getOrder());
        $this->assertSame($product, $orderLine->getProduct());
        $this->assertEquals(2, $orderLine->getQuantity());
    }

    public function testCanConfirmOrder(): void
    {
        $order = new Order('REF-CONFIRM');

        $this->assertEquals('pending', $order->getStatus());

        $order->confirm();

        $this->assertEquals('confirmed', $order->getStatus());
    }

    public function testOrderCanHaveMultipleLines(): void
    {
        $order = new Order('REF-MULTI');
        $product1 = new Product('Product 1', 5.0, 10);
        $product2 = new Product('Product 2', 15.0, 20);

        $orderLine1 = new OrderLine($product1, 3, $order);
        $orderLine2 = new OrderLine($product2, 2, $order);

        $order->addLine($orderLine1);
        $order->addLine($orderLine2);

        $this->assertCount(2, $order->getLines());
        $lines = $order->getLines()->toArray();

        $this->assertSame($product1, $lines[0]->getProduct());
        $this->assertSame($product2, $lines[1]->getProduct());
    }
}
