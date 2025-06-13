<?php

namespace App\Catalog\Application;

use App\Catalog\Domain\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function createProduct(string $name, float $price, int $availableStock): Product
    {
        $product = new Product($name, $price, $availableStock);

        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }

    public function updateStock(Product $product, int $availableStock): void
    {
        $product->setAvailableStock($availableStock);
        $this->em->flush();
    }
}
