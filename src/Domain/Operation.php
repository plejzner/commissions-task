<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Domain;

final class Operation
{
    private string $binNumber;
    private float $amount;
    private string $currencySymbol;

    /**
     * @throws OperationException
     */
    public function __construct(array $operationData)
    {
        $this->binNumber = $operationData['bin'];
        $this->currencySymbol = $operationData['currency'];

        $amount = (float) $operationData['amount'];
        if ($amount <= 0) {
            // let's say that for our context amount must be > 0
            throw new OperationException('Amount must be greater than 0');
        }
        $this->amount = $amount;
    }

    public function getBinNumber(): string
    {
        return $this->binNumber;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrencySymbol(): string
    {
        return $this->currencySymbol;
    }
}
