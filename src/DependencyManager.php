<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Plejzner\CommissionTask\Infrastructure\SymfonyHttpClientAdapter;
use Plejzner\CommissionTask\Service\CardBinApiClient;
use Plejzner\CommissionTask\Service\CommissionCalculator;
use Plejzner\CommissionTask\Service\CommissionPrinter;
use Plejzner\CommissionTask\Service\ExchangeApiClient;
use Plejzner\CommissionTask\Service\Math;
use Plejzner\CommissionTask\Service\OperationsSourceFile;
use Symfony\Component\HttpClient\HttpClient as HttpClientFactory;

final class DependencyManager
{
    /**
     * @throws Domain\ApiClientException
     */
    public static function getCommissionPrinter($fileName): CommissionPrinter
    {
        $symfonyHttpClientAdapter = new SymfonyHttpClientAdapter(new HttpClientFactory());

        return new CommissionPrinter(
            new OperationsSourceFile($fileName),
            new CommissionCalculator(
                new CardBinApiClient($symfonyHttpClientAdapter),
                new ExchangeApiClient($symfonyHttpClientAdapter, Config::EXCHANGE_API_KEY),
                new Math(Config::DECIMAL_PRECISION)
            ),
            self::getLogger()
        );
    }

    public static function getLogger(): Logger
    {
        $logger = new Logger('commission');
        $logger->pushHandler(new StreamHandler(Config::LOG_PATH));

        return $logger;
    }
}
