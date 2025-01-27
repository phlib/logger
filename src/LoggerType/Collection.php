<?php

declare(strict_types=1);

namespace Phlib\Logger\LoggerType;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

/**
 * @package Phlib\Logger
 */
class Collection extends AbstractLogger
{
    /**
     * @var \SplObjectStorage
     */
    protected $loggerInstances;

    public function __construct()
    {
        $this->loggerInstances = new \SplObjectStorage();
    }

    public function add(LoggerInterface $logger): self
    {
        $this->loggerInstances->attach($logger);

        return $this;
    }

    public function remove(LoggerInterface $logger): self
    {
        $this->loggerInstances->detach($logger);

        return $this;
    }

    /**
     * @param mixed $level
     * @param string|\Stringable $message
     */
    public function log($level, $message, array $context = []): void
    {
        /** @var LoggerInterface $logger */
        foreach ($this->loggerInstances as $logger) {
            $logger->log($level, $message, $context);
        }
    }
}
