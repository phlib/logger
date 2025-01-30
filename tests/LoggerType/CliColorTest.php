<?php

declare(strict_types=1);

namespace Phlib\Logger\Test\LoggerType;

use Phlib\Logger\LoggerType\CliColor;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class CliColorTest extends TestCase
{
    public function testLogDebug(): void
    {
        $resource = fopen('php://memory', 'a');
        $logger = new CliColor('name', $resource);

        $message = 'Test Log Message';
        $logger->log(LogLevel::DEBUG, $message);

        rewind($resource);
        $logMessage = fread($resource, 1024);
        static::assertStringNotContainsString("\033[", $logMessage);
        static::assertStringContainsString($message, $logMessage);
    }

    public function testLogInfo(): void
    {
        $resource = fopen('php://memory', 'a');
        $logger = new CliColor('name', $resource);

        $message = 'Test Log Message';
        $logger->log(LogLevel::INFO, $message);

        rewind($resource);
        $logMessage = fread($resource, 1024);
        static::assertStringStartsWith("\033[34m", $logMessage);
        static::assertStringContainsString($message, $logMessage);
        static::assertStringEndsWith("\033[39m\n", $logMessage);
    }

    public function testLogNotice(): void
    {
        $resource = fopen('php://memory', 'a');
        $logger = new CliColor('name', $resource);

        $message = 'Test Log Message';
        $logger->log(LogLevel::NOTICE, $message);

        rewind($resource);
        $logMessage = fread($resource, 1024);
        static::assertStringStartsWith("\033[32m", $logMessage);
        static::assertStringContainsString($message, $logMessage);
        static::assertStringEndsWith("\033[39m" . PHP_EOL, $logMessage);
    }

    public function testLogWarning(): void
    {
        $resource = fopen('php://memory', 'a');
        $logger = new CliColor('name', $resource);

        $message = 'Test Log Message';
        $logger->log(LogLevel::WARNING, $message);

        rewind($resource);
        $logMessage = fread($resource, 1024);
        static::assertStringStartsWith("\033[33m", $logMessage);
        static::assertStringContainsString($message, $logMessage);
        static::assertStringEndsWith("\033[39m" . PHP_EOL, $logMessage);
    }

    public function testLogError(): void
    {
        $resource = fopen('php://memory', 'a');
        $logger = new CliColor('name', $resource);

        $message = 'Test Log Message';
        $logger->log(LogLevel::ERROR, $message);

        rewind($resource);
        $logMessage = fread($resource, 1024);
        static::assertStringStartsWith("\033[31m", $logMessage);
        static::assertStringContainsString($message, $logMessage);
        static::assertStringEndsWith("\033[39m" . PHP_EOL, $logMessage);
    }

    public function testLogCritical(): void
    {
        $resource = fopen('php://memory', 'a');
        $logger = new CliColor('name', $resource);

        $message = 'Test Log Message';
        $logger->log(LogLevel::CRITICAL, $message);

        rewind($resource);
        $logMessage = fread($resource, 1024);
        static::assertStringStartsWith("\033[31;43m", $logMessage);
        static::assertStringContainsString($message, $logMessage);
        static::assertStringEndsWith("\033[39;49m" . PHP_EOL, $logMessage);
    }

    public function testLogAlert(): void
    {
        $resource = fopen('php://memory', 'a');
        $logger = new CliColor('name', $resource);

        $message = 'Test Log Message';
        $logger->log(LogLevel::ALERT, $message);

        rewind($resource);
        $logMessage = fread($resource, 1024);
        static::assertStringStartsWith("\033[37;41;1m", $logMessage);
        static::assertStringContainsString($message, $logMessage);
        static::assertStringEndsWith("\033[39;49;22m" . PHP_EOL, $logMessage);
    }

    public function testLogEmergency(): void
    {
        $resource = fopen('php://memory', 'a');
        $logger = new CliColor('name', $resource);

        $message = 'Test Log Message';
        $logger->log(LogLevel::EMERGENCY, $message);

        rewind($resource);
        $logMessage = fread($resource, 1024);
        static::assertStringStartsWith("\033[37;41;1;4m", $logMessage);
        static::assertStringContainsString($message, $logMessage);
        static::assertStringEndsWith("\033[39;49;22;24m" . PHP_EOL, $logMessage);
    }
}
