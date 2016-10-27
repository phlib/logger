<?php

namespace Phlib\Logger\Decorator;

use Psr\Log\LoggerInterface;

/**
 * Set default context values for log entries
 *
 * Log context values with matching keys will take precedence over defaults
 *
 * @package Phlib\Logger
 */
class DefaultContext extends AbstractDecorator
{
    /**
     * @var array
     */
    private $decorations;

    /**
     * @param LoggerInterface $logger
     * @param array $decorations
     */
    public function __construct(LoggerInterface $logger, array $decorations)
    {
        parent::__construct($logger, $decorations);

        $this->decorations = $decorations;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        $context = array_merge($this->decorations, $context);

        $this->getInnerLogger()->log($level, $message, $context);
    }
}
