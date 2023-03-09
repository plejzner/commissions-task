<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Service;

final readonly class Math
{
    public function __construct(
        private int $decimalPrecision
    ) {
        if ($decimalPrecision < 0) {
            throw new MathException('Decimal precision must be >= 0');
        }
    }

    public function decimalCeiling(float $value): float
    {
        $mult = pow(10, $this->decimalPrecision);

        return ceil($value * $mult) / $mult;
    }
}
