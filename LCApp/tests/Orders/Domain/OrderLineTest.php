<?php

namespace App\Tests\Orders\Domain;

use App\Orders\Domain\Order;
use App\Orders\Domain\OrderLine;
use App\Catalog\Domain\Product;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class OrderLineTest extends TestCase
{
    public function testOrderLineIsCreatedCorrectly(): void
    {
        $order = new Order('REF-ORDERLINE');
        $product = new Product('Product Test', 9.99999, 10);

        $orderLine = new OrderLine($product, 3, $order);

        $this->assertSame($product, $orderLine->getProduct());
        $this->assertEquals(3, $orderLine->getQuantity());
        $this->assertEquals(10.00, $orderLine->getUnitPrice());
        $this->assertSame($order, $orderLine->getOrder());
    }

    public function testGetTotalPrice(): void
    {
        $order = new Order('REF-TOTAL');
        $product = new Product('Product Total', 5.25, 10);

        $orderLine = new OrderLine($product, 4, $order);

        $this->assertEquals(21.00, $orderLine->getTotalPrice());
    }

    public function testCannotCreateOrderLineWithZeroQuantity(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $order = new Order('REF-INVALID');
        $product = new Product('Invalid Product', 10.0, 10);

        new OrderLine($product, 0, $order);
    }

    public function testCannotCreateOrderLineWithNegativeQuantity(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $order = new Order('REF-INVALID-NEGATIVE');
        $product = new Product('Invalid Product Negative', 10.0, 10);

        new OrderLine($product, -5, $order);
    }
}
