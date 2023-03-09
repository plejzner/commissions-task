<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Tests\unit\Service\TestDoubles;

use Plejzner\CommissionTask\Service\HttpClientInterface;

final class HttpClientStub implements HttpClientInterface
{
    private array $returnValues = [];

    public function __construct(string $forUrl = '', array $willReturn = [])
    {
        $this->returnValues[$forUrl] = $willReturn;
    }

    public function makeRequest(string $url, ?array $httpHeaders = null): array
    {
        return $this->returnValues[$url];
    }
}
