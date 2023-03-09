<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Tests\unit\Service\TestDoubles;

use Plejzner\CommissionTask\Domain\CardBinApiClientInterface;

final class CardBinApiStub implements CardBinApiClientInterface
{
    private array $codes = [];

    public function __construct(string $for = '', string $willReturn = '')
    {
        $this->codes[$for] = $willReturn;
    }

    public function getCountryCodeForBin(string $binNumber): string
    {
        return $this->codes[$binNumber];
    }
}
