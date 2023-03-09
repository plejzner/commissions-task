<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Service;

use Plejzner\CommissionTask\Domain\ApiClientException;
use Plejzner\CommissionTask\Domain\ExchangeApiClientInterface;

final class ExchangeApiClient implements ExchangeApiClientInterface
{
    private const API_ENDPOINT_URL = 'https://api.apilayer.com/exchangerates_data/latest';

    /**
     * simple caching for script lifetime.
     */
    private array $cachedRates = [];

    /**
     * @throws ApiClientException
     */
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey
    ) {
        if (empty($this->apiKey)) {
            throw new ApiClientException('No api key provided');
        }
    }

    /**
     * @throws ApiClientException
     */
    public function getExchangeRate(string $currencySymbol): float
    {
        if (empty($this->cachedRates)) {
            $this->cachedRates = $this->getRates();
        }

        if (!isset($this->cachedRates[$currencySymbol])) {
            throw new ApiClientException('unknown currency symbol');
        }

        return $this->cachedRates[$currencySymbol];
    }

    /**
     * @throws ApiClientException
     */
    private function getRates(): array
    {
        $authHeader = 'apikey: '.$this->apiKey;
        $jsonResponse = $this->httpClient->makeRequest(self::API_ENDPOINT_URL, [$authHeader]);

        if (isset($jsonResponse['error'])) {
            throw new ApiClientException('Api request failed: '.$jsonResponse['error']);
        }

        if (!isset($jsonResponse['rates']) || !is_array($jsonResponse['rates'])) {
            throw new ApiClientException('malformed api response: '.var_export($jsonResponse, true));
        }

        return $jsonResponse['rates'];
    }
}
