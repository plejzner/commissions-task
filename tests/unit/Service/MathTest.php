<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Tests\unit\Service;

use Generator;
use PHPUnit\Framework\TestCase;
use Plejzner\CommissionTask\Service\Math;
use Plejzner\CommissionTask\Service\MathException;

class MathTest extends TestCase
{
    /**
     * @dataProvider numbersForCeilingProvider
     */
    public function testDecimalCeiling(float $number, int $precision, float $expectedResult)
    {
        $math = new Math($precision);

        self::assertEquals(
            $expectedResult,
            $math->decimalCeiling($number)
        );
    }

    public function testExceptionOnNegativePrecision()
    {
        self::expectException(MathException::class);
        new Math(-1);
    }

    private function numbersForCeilingProvider(): Generator
    {
        yield [1.0,         2, 1];
        yield [1.000000001, 2, 1.01];
        yield [1.5,         2, 1.50];
        yield [1.51,        2, 1.51];
        yield [1.50001,     2, 1.51];
        yield [1.40001,     2, 1.41];
        yield [0,           2, 0];
        yield [1.40001,     1, 1.5];
        yield [1.40001,     0, 2];
        yield [-1.40001,    2, -1.40];
        yield [-1.40001,    3, -1.4];
        yield [1.40001,     3, 1.401];
    }
}
