<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Service;

use Plejzner\CommissionTask\Domain\ApiClientException;
use Plejzner\CommissionTask\Domain\CardBinApiClientInterface;
use Plejzner\CommissionTask\Domain\EUCountryCodeEnum;
use Plejzner\CommissionTask\Domain\ExchangeApiClientInterface;
use Plejzner\CommissionTask\Domain\Operation;

final class CommissionCalculator
{
    private const COMMISSION_RATE_DEFAULT = 0.02;
    private const COMMISSION_RATE_EU = 0.01;

    public function __construct(
        private readonly CardBinApiClientInterface $cardBinApi,
        private readonly ExchangeApiClientInterface $exchangeApi,
        private readonly Math $math
    ) {
    }

    /**
     * @throws CommissionCalculatorException
     * @throws ApiClientException
     */
    public function calculate(Operation $operation): float
    {
        $exchangeRate = $this->exchangeApi->getExchangeRate($operation->getCurrencySymbol());
        if ($exchangeRate <= 0) {
            throw new CommissionCalculatorException('Exchange rate must be greater than 0');
        }
        $amountInEuro = $operation->getAmount() / $exchangeRate;

        $countryCode = $this->cardBinApi->getCountryCodeForBin($operation->getBinNumber());

        $commissionRate = self::COMMISSION_RATE_DEFAULT;
        if (EUCountryCodeEnum::contains($countryCode)) {
            $commissionRate = self::COMMISSION_RATE_EU;
        }

        $commission = $amountInEuro * $commissionRate;

        return $this->math->decimalCeiling($commission);
    }
}
