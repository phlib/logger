<?php

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
     * @var mixed
     */
    protected $config;

    /**
     * @param LoggerInterface $logger
     * @param mixed $config
     */
    public function __construct(LoggerInterface $logger, $config)
    {
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @return LoggerInterface
     */
    protected function getInnerLogger()
    {
        return $this->logger;
    }
}
