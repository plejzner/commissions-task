<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask;

require_once 'vendor/autoload.php';

if (!isset($argv[1])) {
    throw new \RuntimeException('Parameter with file name is required');
}
$fileName = $argv[1];

// I assume we want exceptions that bubble up to here, to be shown on screen. And script will return non 0 control code.
$commissionPrinter = DependencyManager::getCommissionPrinter($fileName);
$commissionPrinter->print();
