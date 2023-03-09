<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Tests\integration;

use PHPUnit\Framework\TestCase;
use Plejzner\CommissionTask\Config;
use Plejzner\CommissionTask\Domain\CardBinApiClientInterface;
use Plejzner\CommissionTask\Domain\ExchangeApiClientInterface;
use Plejzner\CommissionTask\Service\CommissionCalculator;
use Plejzner\CommissionTask\Service\CommissionPrinter;
use Plejzner\CommissionTask\Service\Math;
use Plejzner\CommissionTask\Service\OperationsSourceFile;
use Plejzner\CommissionTask\Tests\integration\TestDoubles\LoggerStub;

/**
 * Integration test for whole use case of legacy script
 */
class CommissionPrintingTest extends TestCase
{
    private const INPUT_FILE_PATH = __DIR__ . '/input.txt';
    private const EXPECTED_LINES = ['1', '0.47', '1.41', '2.41', '44.85', ''];

    public function testCommissionPrintingForInputFile()
    {
        $commissionPrinter = new CommissionPrinter(
            new OperationsSourceFile(self::INPUT_FILE_PATH),
            new CommissionCalculator(
                $this->getCardBinApiClientStub(),
                $this->getExchangeApiClientStub(),
                new Math(Config::DECIMAL_PRECISION)
            ),
            new LoggerStub
        );

        ob_start();
        $commissionPrinter->print();
        $output = ob_get_clean();

        $outputLines = explode("\n", $output);
        self::assertEquals(self::EXPECTED_LINES, $outputLines);
    }

    private function getExchangeApiClientStub(): ExchangeApiClientInterface
    {
        $stub = $this->createStub(ExchangeApiClientInterface::class);

        $map = [
            ['EUR', 1.0],
            ['USD', 1.082778],
            ['JPY', 142.270065],
            ['GBP', 0.891897],
        ];

        $stub->method('getExchangeRate')
            ->will($this->returnValueMap($map));

        return $stub;
    }

    private function getCardBinApiClientStub(): CardBinApiClientInterface
    {
        $stub = $this->createStub(CardBinApiClientInterface::class);

        $map = [
            ['45717360', 'DK'],
            ['516793', 'LT'],
            ['45417360', 'JP'],
            ['41417360', 'US'],
            ['4745030', 'GB'],
        ];

        $stub->method('getCountryCodeForBin')
            ->will($this->returnValueMap($map));

        return $stub;
    }
}
