<?php

namespace App\Orders\Infrastructure;

use App\Orders\Domain\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }
    public function save(Order $order): void
    {
        $this->_em->persist($order);
        $this->_em->flush();
    }

    public function remove(Order $order): void
    {
        $this->_em->remove($order);
        $this->_em->flush();
    }

}
