<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Infrastructure;

use Plejzner\CommissionTask\Service\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface as SymfonyHttpClientInterface;

final readonly class SymfonyHttpClientAdapter implements HttpClientInterface
{
    private const DEFAULT_METHOD = 'GET';
    private SymfonyHttpClientInterface $symfonyHttpClient;

    public function __construct(HttpClient $symfonyHttpClientFactory)
    {
        $this->symfonyHttpClient = $symfonyHttpClientFactory::create();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function makeRequest(string $url, ?array $httpHeaders = null): array
    {
        $options = ['headers' => $httpHeaders];
        $response = $this->symfonyHttpClient->request(self::DEFAULT_METHOD, $url, $options);

        return $response->toArray();
    }
}
