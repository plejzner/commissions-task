<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Domain;

interface CardBinApiClientInterface
{
    /**
     * @throws ApiClientException
     */
    public function getCountryCodeForBin(string $binNumber): string;
}
