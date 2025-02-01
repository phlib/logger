<?php

declare(strict_types=1);

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
    private readonly array $decorations;

    public function __construct(LoggerInterface $logger, array $decorations)
    {
        parent::__construct($logger, $decorations);

        $this->decorations = $decorations;
    }

    /**
     * @param mixed $level
     * @param string|\Stringable $message
     */
    public function log($level, $message, array $context = []): void
    {
        $context = array_merge($this->decorations, $context);

        $this->getInnerLogger()->log($level, $message, $context);
    }
}
