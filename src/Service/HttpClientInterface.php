<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Service;

interface HttpClientInterface
{
    public function makeRequest(string $url, ?array $httpHeaders = null): array;
}
