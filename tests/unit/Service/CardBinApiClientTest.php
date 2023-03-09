<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Tests\unit\Service;

use Generator;
use PHPUnit\Framework\TestCase;
use Plejzner\CommissionTask\Domain\ApiClientException;
use Plejzner\CommissionTask\Service\CardBinApiClient;
use Plejzner\CommissionTask\Tests\unit\Service\TestDoubles\HttpClientStub;

class CardBinApiClientTest extends TestCase
{
    public function testItWillReturnCountryCodeForProperHttpClientResponse()
    {
        $givenBin = '45717360';
        $expectedCountryCode = 'DK';

        $client = new CardBinApiClient(
            new HttpClientStub(
                forUrl: "https://lookup.binlist.net/$givenBin",
                willReturn: ['country' => ['alpha2' => 'DK']]
            )
        );

        $countryCode = $client->getCountryCodeForBin($givenBin);

        self::assertEquals(
            $countryCode,
            $expectedCountryCode
        );
    }

    /**
     * @dataProvider invalidHttpClientResponses
     */
    public function testItWillThrowExceptionOnInvalidArrayStructure(
        $httpClientResponse
    )
    {
        $givenBin = '516793';

        $client = new CardBinApiClient(
            new HttpClientStub(
                forUrl: "https://lookup.binlist.net/$givenBin",
                willReturn: $httpClientResponse
            )
        );

        self::expectException(ApiClientException::class);

        $client->getCountryCodeForBin($givenBin);
    }

    private function invalidHttpClientResponses(): Generator
    {
        yield ['country' => ['BAD-KEY' => 'US']];
        yield ['country' => []];
        yield [[]];
    }
}
