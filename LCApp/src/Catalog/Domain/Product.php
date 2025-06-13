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

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $price;

    #[ORM\Column(type: 'integer')]
    private int $availableStock;

    public function __construct(string $name, float $price, int $availableStock)
    {
        $this->name = $name;
        $this->setPrice($price);
        $this->availableStock = $availableStock;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }

    public function getPrice(): float
    {
        return (float) $this->price;
    }

    public function setPrice(float $price): void
    {
        if ($price < 0) {
            throw new \InvalidArgumentException('Price cannot be negative.');
        }
        $this->price = number_format(round($price, 2), 2, '.', '');
    }

    public function getAvailableStock(): int { return $this->availableStock; }

    public function setAvailableStock(int $stock): void
    {
        if ($stock < 0) {
            throw new \InvalidArgumentException('Stock cannot be negative.');
        }

        $this->availableStock = $stock;
    }

    public function decreaseStock(int $amount): void
    {
        if ($amount > $this->availableStock) {
            throw new \DomainException('Not enough stock available.');
        }

        $this->availableStock -= $amount;
    }
}
