<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Service;

use Plejzner\CommissionTask\Domain\OperationsSource;
use Psr\Log\LoggerInterface;

final readonly class CommissionPrinter
{
    public function __construct(
        private OperationsSource $operationsSource,
        private CommissionCalculator $commissionCalculator,
        private LoggerInterface $logger
    ) {
    }

    /**
     * This will print commission or error for operations in respective lines.
     */
    public function print(): void
    {
        foreach ($this->operationsSource->getOperation() as $operation) {
            if ($operation instanceof \Throwable) {
                $this->handleError($operation);
                continue;
            }
            try {
                echo $this->commissionCalculator->calculate($operation)."\n";
            } catch (\Throwable $e) {
                $this->handleError($e);
            }
        }
    }

    private function handleError($e): void
    {
        $this->logger->error($e);
        // and echo message (so order of lines in output is preserved)
        echo "Error while calculating commission for this operation (see log for details)\n";
    }
}
