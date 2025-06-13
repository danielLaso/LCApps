<?php

namespace App\Catalog\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Catalog\Infrastructure\DoctrineProductRepository')]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'float')]
    private float $price;

    #[ORM\Column(type: 'integer')]
    private int $availableStock;

    public function __construct(string $name, float $price, int $availableStock)
    {
        $this->name = $name;
        $this->price = $price;
        $this->availableStock = $availableStock;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getPrice(): float { return $this->price; }
    public function getAvailableStock(): int { return $this->availableStock; }

    public function setAvailableStock(int $stock): void { $this->availableStock = $stock; }
}
