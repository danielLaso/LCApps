<?php

namespace App\Catalog\Domain\ValueObject;

final class Money
{
    private string $amount;

    public function __construct(float $amount)
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative.');
        }

        $this->amount = number_format(round($amount, 2), 2, '.', '');
    }

    public function getAmount(): float
    {
        return (float) $this->amount;
    }

    public function __toString(): string
    {
        return $this->amount;
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount;
    }
}
