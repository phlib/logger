<?php

declare(strict_types=1);

namespace Phlib\Logger\LoggerType;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
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
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = []): void
    {
        foreach ($this->loggerInstances as $logger) {
            $logger->log($level, $message, $context);
        }
    }
}
