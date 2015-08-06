<?php

namespace Phlib\Logger;

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

    /**
     *
     */
    public function __construct()
    {
        $this->loggerInstances = new \SplObjectStorage();
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function add(LoggerInterface $logger)
    {
        $this->loggerInstances->attach($logger);

        return $this;
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function remove(LoggerInterface $logger)
    {
        $this->loggerInstances->detach($logger);

        return $this;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        foreach ($this->loggerInstances as $logger) {
            $logger->log($level, $message, $context);
        }
    }
}
