<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Tests\unit\Service\TestDoubles;

use Plejzner\CommissionTask\Service\HttpClientInterface;
use function PHPUnit\Framework\assertEquals;

final class HttpClientWithAuthMock implements HttpClientInterface
{
    private array $returnValues = [];

    public function __construct(string $forUrl, array $andHeaders, array $willReturn)
    {
        $this->returnValues[$forUrl]['response'] = $willReturn;
        $this->returnValues[$forUrl]['headers'] = $andHeaders;
    }

    public function makeRequest(string $url, array $httpHeaders = null): array
    {
        assertEquals(
            $this->returnValues[$url]['headers'],
            $httpHeaders
        );

        return $this->returnValues[$url]['response'];
    }
}
