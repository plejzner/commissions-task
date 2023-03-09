<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Tests\unit\Service;

use PHPUnit\Framework\TestCase;
use Plejzner\CommissionTask\Config;
use Plejzner\CommissionTask\Domain\ExchangeApiClientInterface;
use Plejzner\CommissionTask\Domain\Operation;
use Plejzner\CommissionTask\Service\CommissionCalculator;
use Plejzner\CommissionTask\Service\CommissionCalculatorException;
use Plejzner\CommissionTask\Service\Math;
use Plejzner\CommissionTask\Tests\unit\Service\TestDoubles\CardBinApiStub;
use Plejzner\CommissionTask\Tests\unit\Service\TestDoubles\ExchangeApiStub;

class CommissionCalculatorTest extends TestCase
{
    /**
     * @var int
     */
    private const DECIMAL_PRECISION_TEST = 2;

    /**
     * @dataProvider correctOperationsProvider
     */
    public function testCommissionCalculation(
        Operation $operation,
        float $exchangeRate,
        string $countryCode,
        float $expectedCommission
    ) {
        $calc = new CommissionCalculator(
            new CardBinApiStub(for: $operation->getBinNumber(), willReturn: $countryCode),
            new ExchangeApiStub(for: $operation->getCurrencySymbol(), willReturn: $exchangeRate),
            new Math(self::DECIMAL_PRECISION_TEST)
        );

        $commission = $calc->calculate($operation);

        self::assertEquals($expectedCommission, $commission);
    }

    /**
     * @dataProvider badRateProvider
     */
    public function testCalculatorShouldThrowExceptionOnNegativeOrZeroExchangeRate(
        ExchangeApiClientInterface $exchangeApiClientStub
    ){
        $calc = new CommissionCalculator(
            new CardBinApiStub,
            $exchangeApiClientStub,
            new Math(Config::DECIMAL_PRECISION)
        );

        self::expectException(CommissionCalculatorException::class);

        $calc->calculate(
            new Operation([
                'bin' => '45717360',
                'amount' => 1.0,
                'currency' => 'EUR',
            ])
        );
    }

    private function correctOperationsProvider(): \Generator
    {
        yield 'operation in EUR, card from EU' => [
            'operation' => new Operation([
                'bin' => '45717360',
                'amount' => 100.00,
                'currency' => 'EUR',
            ]),
            'exchangeRate' => 1.0,
            'countryCode' => 'DK',
            'expectedCommission' => 1.0
        ];

        yield 'operation in USD, card from EU' => [
            'operation' => new Operation([
                'bin' => '516793',
                'amount' => 50.00,
                'currency' => 'USD',
            ]),
            'exchangeRate' => 1.082778,
            'countryCode' => 'LT',
            'expectedCommission' => 0.47
        ];

        yield 'operation in GBP, card NOT from EU' => [
            'operation' => new Operation([
                'bin' => '41417360',
                'amount' => 123.12,
                'currency' => 'GBP',
            ]),
            'exchangeRate' => 0.892111,
            'countryCode' => 'US',
            'expectedCommission' => 2.77
        ];
    }

    private function badRateProvider(): \Generator
    {
        yield 'negative exchange rate' => [
            new ExchangeApiStub(for: 'EUR', willReturn: -1.0)
        ];

        yield 'zero exchange rate' => [
            new ExchangeApiStub(for: 'EUR', willReturn: 0)
        ];
    }
}
