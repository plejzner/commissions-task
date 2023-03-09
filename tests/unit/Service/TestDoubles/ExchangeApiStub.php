<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Tests\unit\Service\TestDoubles;

use Plejzner\CommissionTask\Domain\ExchangeApiClientInterface;

final class ExchangeApiStub implements ExchangeApiClientInterface
{
    private array $rates = [];

    public function __construct(string $for = '', float $willReturn = 1)
    {
        $this->rates[$for] = $willReturn;
    }

    public function getExchangeRate(string $currencySymbol): float
    {
        return $this->rates[$currencySymbol];
    }
}
