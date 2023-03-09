<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Tests\unit\Service;

use PHPUnit\Framework\TestCase;
use Plejzner\CommissionTask\Domain\ApiClientException;
use Plejzner\CommissionTask\Service\ExchangeApiClient;
use Plejzner\CommissionTask\Tests\unit\Service\TestDoubles\HttpClientStub;
use Plejzner\CommissionTask\Tests\unit\Service\TestDoubles\HttpClientWithAuthMock;

final class ExchangeApiClientTest extends TestCase
{
    public function testItWillPassProperHeadersAndReturnExpectedRate()
    {
        $expectedRate = 1.072881;
        $testApiKey = 'testApiKey123';

        $client = new ExchangeApiClient(
            new HttpClientWithAuthMock(
                forUrl: 'https://api.apilayer.com/exchangerates_data/latest',
                andHeaders: ["apikey: $testApiKey"],
                willReturn: [
                    'success' => true,
                    'rates' => ['USD' => $expectedRate]
                ]
            ),
            apiKey: $testApiKey
        );

        $rate = $client->getExchangeRate('USD');

        self::assertEquals(
            $rate,
            $expectedRate
        );
    }

    public function testItWillThrowExceptionOnEmptyApiKey()
    {
        self::expectException(ApiClientException::class);
        self::expectExceptionMessage('No api key provided');

        new ExchangeApiClient(
            new HttpClientStub,
            apiKey: ''
        );
    }

    public function testItWillThrowExceptionOnUnknownCurrency()
    {
        $client = new ExchangeApiClient(
            new HttpClientStub(
                forUrl: 'https://api.apilayer.com/exchangerates_data/latest',
                willReturn: [
                    'success' => true,
                    'rates' => ['USD' => 1.2]
                ]
            ),
            apiKey: 'abc123'
        );

        self::expectException(ApiClientException::class);
        self::expectExceptionMessage('unknown currency symbol');

        $client->getExchangeRate('ABC');
    }
}
