<?php

declare(strict_types=1);

namespace Phlib\Logger\Decorator;

use Psr\Log\LogLevel;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;

/**
 * Class LevelFilter
 * @package Phlib\Logger
 */
class LevelFilter extends AbstractDecorator
{
    /**
     * Logging levels from syslog protocol defined in RFC 5424
     *
     * @var string[] $levels Logging levels
     */
    private static $levels = [
        LogLevel::EMERGENCY, // 0
        LogLevel::ALERT,     // 1
        LogLevel::CRITICAL,  // 2
        LogLevel::ERROR,     // 3
        LogLevel::WARNING,   // 4
        LogLevel::NOTICE,    // 5
        LogLevel::INFO,      // 6
        LogLevel::DEBUG      // 7
    ];

    /**
     * @var int
     */
    private $logLevel;

    public function __construct(LoggerInterface $logger, string $level)
    {
        parent::__construct($logger, $level);

        $this->logLevel = array_search($level, self::$levels, true);
        if ($this->logLevel === false) {
            throw new InvalidArgumentException(
                sprintf(
                    'Cannot use logging level "%s"',
                    $level
                )
            );
        }
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
        $levelCode = array_search($level, self::$levels, true);
        if ($levelCode === false) {
            throw new InvalidArgumentException(
                sprintf(
                    'Cannot use unknown logging level "%s"',
                    $level
                )
            );
        }
        if ($levelCode > $this->logLevel) {
            return;
        }

        $this->getInnerLogger()->log($level, $message, $context);
    }
}
