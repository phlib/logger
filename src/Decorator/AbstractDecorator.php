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
     * Stores the Logger for re-use in the concrete via getInnerLogger()
     *
     * If the concrete requires use of the config value, override the constructor
     * to add validation and store for re-use
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        mixed $config,
    ) {
    }

    protected function getInnerLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
