<?php

namespace App\Catalog\Infrastructure;

use App\Catalog\Domain\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
    public function save(Product $product): void
    {
        $this->_em->persist($product);
        $this->_em->flush();
    }

    public function remove(Product $product): void
    {
        $this->_em->remove($product);
        $this->_em->flush();
    }

}
