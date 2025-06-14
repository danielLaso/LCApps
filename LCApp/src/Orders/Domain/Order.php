<?php

namespace App\Orders\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity(repositoryClass: 'App\Orders\Infrastructure\DoctrineOrderRepository')]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $status;

    #[ORM\Column(type: 'string', length: 100)]
    private string $reference;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderLine::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $lines;

    public function __construct(string $reference)
    {
        $this->reference = $reference;
        $this->status = 'pending';
        $this->lines = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getStatus(): string { return $this->status; }
    public function getReference(): string { return $this->reference; }

    public function getLines(): Collection
    {
        return $this->lines;
    }

    public function addLine(OrderLine $line): void
    {
        $this->lines->add($line);
        $line->setOrder($this);
    }

    public function confirm(): void
    {
        if ($this->lines->isEmpty()) {
            throw new DomainException('Cannot confirm an empty order.');
        }
        
        $this->status = 'confirmed';
    }
}
