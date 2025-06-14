<?php

namespace App\Orders\Domain;

use App\Catalog\Domain\Product;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'order_lines')]
class OrderLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $unitPrice;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'lines')]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function __construct(Product $product, int $quantity, Order $order)
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be positive.');
        }

        $this->product = $product;
        $this->quantity = $quantity;
        $this->unitPrice = number_format(round($product->getPrice(), 2), 2, '.', '');
        $this->order = $order;
    }

    public function getId(): ?int { return $this->id; }
    public function getProduct(): Product { return $this->product; }
    public function getQuantity(): int { return $this->quantity; }
    public function getUnitPrice(): float { return (float) $this->unitPrice; }
    public function getOrder(): Order { return $this->order; }

    public function getTotalPrice(): float
    {
        return $this->quantity * (float) $this->unitPrice;
    }
}
