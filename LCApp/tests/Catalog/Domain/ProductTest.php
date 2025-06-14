<?php

namespace App\Tests\Catalog\Domain;

use App\Catalog\Domain\Product;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use DomainException;

class ProductTest extends TestCase
{
    public function testProductIsCreatedCorrectly(): void
    {
        $product = new Product('Test Product', 19.99999, 10);

        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals(20.00, $product->getPrice());
        $this->assertEquals(10, $product->getAvailableStock());
    }

    public function testCannotSetNegativeStock(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $product = new Product('Product with stock', 10.0, 5);
        $product->setAvailableStock(-3);
    }

    public function testCannotSetNegativePrice(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Product('Invalid price', -5.0, 5);
    }

    public function testDecreaseStockWorksCorrectly(): void
    {
        $product = new Product('Stock Product', 5.0, 10);

        $product->decreaseStock(3);

        $this->assertEquals(7, $product->getAvailableStock());
    }

    public function testDecreaseStockThrowsIfInsufficient(): void
    {
        $this->expectException(DomainException::class);

        $product = new Product('Low Stock', 10.0, 2);

        $product->decreaseStock(5);
    }
}
