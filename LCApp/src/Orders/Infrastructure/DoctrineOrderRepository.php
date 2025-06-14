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
        $em = $this->getEntityManager();
        $em->persist($order);
        $em->flush();
    }

    public function remove(Order $order): void
    {
        $em = $this->getEntityManager();
        $em->remove($order);
        $em->flush();
    }

}
