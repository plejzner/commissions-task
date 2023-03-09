<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Domain;

/**
 * @throws OperationsSourceException
 */
interface OperationsSource
{
    public function getOperation(): \Generator;
}
