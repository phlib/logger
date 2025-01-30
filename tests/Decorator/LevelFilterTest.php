<?php

declare(strict_types=1);

namespace Phlib\Logger\Test\Decorator;

use Phlib\Logger\Decorator\LevelFilter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class LevelFilterTest extends TestCase
{
    public function testIsPsrLog(): void
    {
        $levelFilter = new LevelFilter($this->getMockLoggerInterface(), LogLevel::DEBUG);
        static::assertInstanceOf(LoggerInterface::class, $levelFilter);
    }

    public function testLog(): void
    {
        $loggerInterface = $this->getMockLoggerInterface();

        // We expect this test to call the loggerInterface twice to log the ERROR and CRITICAL messages
        // and to ignore the WARNING log message
        $loggerInterface->expects(static::exactly(2))
            ->method('log')
            ->withConsecutive(
                [LogLevel::ERROR],
                [LogLevel::CRITICAL]
            );

        $levelFilter = new LevelFilter($loggerInterface, LogLevel::ERROR);

        // Log message with equal priority to the level filter (should be get logged)
        $levelFilter->log(LogLevel::ERROR, 'TEST ERROR MESSAGE');

        // Log message with higher priority than the level filter (should be logged)
        $levelFilter->log(LogLevel::CRITICAL, 'TEST CRITICAL MESSAGE');

        // Log message with lower priority than the level filter (should not be logged)
        $levelFilter->log(LogLevel::WARNING, 'TEST WARNING MESSAGE');
    }

    public function testInvalidConstructorLogLevel(): void
    {
        // We expect an exception to be thrown when specifying an invalid logging level in the constructor
        $this->expectException(\Psr\Log\InvalidArgumentException::class);

        new LevelFilter($this->getMockLoggerInterface(), 'InvalidLogLevel');
    }

    public function testInvalidLogLogLevel(): void
    {
        // We expect an exception to be thrown when specifying an invalid logging level in the log method parameter
        $this->expectException(\Psr\Log\InvalidArgumentException::class);

        $levelFilter = new LevelFilter($this->getMockLoggerInterface(), LogLevel::DEBUG);
        $levelFilter->log('InvalidLogLevel', 'TEST LOG MESSAGE');
    }

    private function getMockLoggerInterface(): LoggerInterface&MockObject
    {
        return $this->createMock(LoggerInterface::class);
    }
}
