<?php

namespace App\Tests\Orders\Domain;

use App\Orders\Domain\Order;
use App\Orders\Domain\OrderLine;
use App\Catalog\Domain\Product;
use App\Catalog\Domain\ValueObject\Money;
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
        $product = new Product('Product test', new Money(10.0), 5);

        $orderLine = new OrderLine($product, 2, $order);

        $order->addLine($orderLine);

        $this->assertCount(1, $order->getLines());
        $this->assertSame($order, $orderLine->getOrder());
        $this->assertSame($product, $orderLine->getProduct());
        $this->assertEquals(2, $orderLine->getQuantity());
    }

    public function testCanConfirmOrderWithStock(): void
    {
        $order = new Order('REF-CONFIRM-STOCK');
        $product = new Product('Product A', new Money(10.0), 10);

        $orderLine = new OrderLine($product, 5, $order);
        $order->addLine($orderLine);

        // Simular la lógica de confirmación (como en el controller)
        foreach ($order->getLines() as $line) {
            $product->decreaseStock($line->getQuantity());
        }
        $order->confirm();

        $this->assertEquals('confirmed', $order->getStatus());
        $this->assertEquals(5, $product->getAvailableStock());
    }

    public function testCannotConfirmOrderIfStockIsInsufficient(): void
    {
        $order = new Order('REF-CONFIRM-FAIL');
        $product = new Product('Product B', new Money(15.0), 3);

        $orderLine = new OrderLine($product, 5, $order);
        $order->addLine($orderLine);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Not enough stock available.');

        // Simular la lógica de confirmación (como en el controller)
        foreach ($order->getLines() as $line) {
            $product->decreaseStock($line->getQuantity()); // Aquí lanzará excepción
        }

        $order->confirm(); // Esta línea no se llegará a ejecutar si lanza la excepción
    }

    public function testOrderCanHaveMultipleLines(): void
    {
        $order = new Order('REF-MULTI');
        $product1 = new Product('Product 1', new Money(5.0), 10);
        $product2 = new Product('Product 2', new Money(15.0), 20);

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
