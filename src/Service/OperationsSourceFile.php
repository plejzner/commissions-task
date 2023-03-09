<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Service;

use Plejzner\CommissionTask\Domain\Operation;
use Plejzner\CommissionTask\Domain\OperationsSource;
use Plejzner\CommissionTask\Domain\OperationsSourceException;

final class OperationsSourceFile implements OperationsSource
{
    /**
     * @var resource
     */
    private $fileHandle;

    /**
     * @throws OperationsSourceException
     */
    public function __construct(string $inputFilePath)
    {
        if (file_exists($inputFilePath) === false) {
            throw new OperationsSourceException('File does not exist: '.$inputFilePath);
        }

        $handle = fopen($inputFilePath, 'r');
        if ($handle === false) {
            throw new OperationsSourceException('Error while reading file: '.$inputFilePath);
        }

        $this->fileHandle = $handle;
    }

    /**
     * Read and yield line by line for memory optimization / big files support.
     */
    public function getOperation(): \Generator
    {
        while (($line = fgets($this->fileHandle)) !== false) {
            try {
                $operationJson = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
                yield new Operation($operationJson);
            } catch (\Throwable $e) {
                yield $e;
            }
        }

        fclose($this->fileHandle);
    }
}
