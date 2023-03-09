<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Service;

use Plejzner\CommissionTask\Domain\ApiClientException;
use Plejzner\CommissionTask\Domain\CardBinApiClientInterface;

final class CardBinApiClient implements CardBinApiClientInterface
{
    private const API_ENDPOINT_URL = 'https://lookup.binlist.net/';

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    /**
     * @throws ApiClientException
     */
    public function getCountryCodeForBin(string $binNumber): string
    {
        $url = self::API_ENDPOINT_URL.$binNumber;
        $jsonResponse = $this->httpClient->makeRequest($url);

        if (!isset($jsonResponse['country']['alpha2'])) {
            throw new ApiClientException('no data for provided for bin: '.$binNumber);
        }

        return $jsonResponse['country']['alpha2'];
    }
}
