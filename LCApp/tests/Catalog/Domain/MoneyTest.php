<?php

namespace App\Tests\Catalog\Domain;

use PHPUnit\Framework\TestCase;
use App\Catalog\Domain\ValueObject\Money;
use InvalidArgumentException;

class MoneyTest extends TestCase
{
    public function testCannotCreateMoneyWithNegativeAmount(): void
    {
        $this->expectException(InvalidArgumentException::class);
    
        new Money(-5.0);
    }
    
}
