<?php

declare(strict_types=1);

namespace Phlib\Logger\Decorator;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

/**
 * @package Phlib\Logger
 */
abstract class AbstractDecorator extends AbstractLogger
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AbstractDecorator constructor
     *
     * Stores the Logger for re-use in the concrete via getInnerLogger()
     *
     * If the concrete requires use of the config value, override the constructor
     * to add validation and store for re-use
     *
     * @param LoggerInterface $logger
     * @param mixed $config
     */
    public function __construct(LoggerInterface $logger, $config)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    protected function getInnerLogger()
    {
        return $this->logger;
    }
}
