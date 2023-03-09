<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Tests\unit\Domain;

use Plejzner\CommissionTask\Domain\Operation;
use PHPUnit\Framework\TestCase;
use Plejzner\CommissionTask\Domain\OperationException;

class OperationTest extends TestCase
{
    public function testOperationCanBeConstructedWithProperValues()
    {
        $operation = new Operation([
            'bin' => '45717360',
            'amount' => 100.00,
            'currency' => 'EUR'
        ]);

        self::assertEquals('45717360', $operation->getBinNumber());
        self::assertEquals(100.00, $operation->getAmount());
        self::assertEquals('EUR', $operation->getCurrencySymbol());
    }

    public function testOperationCannotHaveNegativeAmount()
    {
        self::expectException(OperationException::class);

        new Operation([
            'bin' => '45717360',
            'amount' => -1.0,
            'currency' => 'EUR'
        ]);
    }
}
