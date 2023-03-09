<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Domain;

interface ExchangeApiClientInterface
{
    /**
     * @throws ApiClientException
     */
    public function getExchangeRate(string $currencySymbol): float;
}
