<?php

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
    private static $levels = array(
        LogLevel::EMERGENCY, // 0
        LogLevel::ALERT,     // 1
        LogLevel::CRITICAL,  // 2
        LogLevel::ERROR,     // 3
        LogLevel::WARNING,   // 4
        LogLevel::NOTICE,    // 5
        LogLevel::INFO,      // 6
        LogLevel::DEBUG      // 7
    );

    /**
     * @param LoggerInterface $logger
     * @param int $config
     */
    public function __construct(LoggerInterface $logger, $config)
    {
        $config = array_search($config, self::$levels, true);
        if ($config === false) {
            throw new InvalidArgumentException(
                sprintf(
                    'Cannot use logging level "%s"',
                    $config
                )
            );
        }

        parent::__construct($logger, $config);
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
        $levelCode = array_search($level, self::$levels, true);
        if ($levelCode === false) {
            throw new InvalidArgumentException(
                sprintf(
                    'Cannot use unknown logging level "%s"',
                    $level
                )
            );
        }
        if ($levelCode > $this->config) {
            return;
        }

        $this->getInnerLogger()->log($level, $message, $context);
    }
}
