<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask;

final class Config
{
    /**
     * @var string
     */
    public const EXCHANGE_API_KEY = '';

    /**
     * @var string
     */
    public const LOG_PATH = __DIR__.'/../var/log.log';

    /**
     * @var int
     */
    public const DECIMAL_PRECISION = 2;
}
