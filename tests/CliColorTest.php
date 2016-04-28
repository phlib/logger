<?php
namespace Phlib\Logger\Test;

use Phlib\Logger\CliColor;
use Psr\Log\LogLevel;

class CliColorTest extends \PHPUnit_Framework_TestCase
{
    public function testLogDebug()
    {
        $resource = fopen('php://memory', 'a');
        $logger = new CliColor('name', $resource);

        $message = 'Test Log Message';
        $logger->log(LogLevel::DEBUG, $message);

        rewind($resource);
        $logMessage = fread($resource, 1024);
        $this->assertNotContains("\033[", $logMessage);
        $this->assertContains($message, $logMessage);
    }

    public function testLogInfo()
    {
        $resource = fopen('php://memory', 'a');
        $logger = new CliColor('name', $resource);

        $message = 'Test Log Message';
        $logger->log(LogLevel::INFO, $message);

        rewind($resource);
        $logMessage = fread($resource, 1024);
        $this->assertStringStartsWith("\033[34m", $logMessage);
        $this->assertContains($message, $logMessage);
        $this->assertStringEndsWith("\033[39m\n", $logMessage);
    }

    public function testLogNotice()
    {
        $resource = fopen('php://memory', 'a');
        $logger = new CliColor('name', $resource);

        $message = 'Test Log Message';
        $logger->log(LogLevel::NOTICE, $message);

        rewind($resource);
        $logMessage = fread($resource, 1024);
        $this->assertStringStartsWith("\033[32m", $logMessage);
        $this->assertContains($message, $logMessage);
        $this->assertStringEndsWith("\033[39m\n", $logMessage);
    }

    public function testLogWarning()
    {
        $resource = fopen('php://memory', 'a');
        $logger = new CliColor('name', $resource);

        $message = 'Test Log Message';
        $logger->log(LogLevel::WARNING, $message);

        rewind($resource);
        $logMessage = fread($resource, 1024);
        $this->assertStringStartsWith("\033[33m", $logMessage);
        $this->assertContains($message, $logMessage);
        $this->assertStringEndsWith("\033[39m\n", $logMessage);
    }

    public function testLogError()
    {
        $resource = fopen('php://memory', 'a');
        $logger = new CliColor('name', $resource);

        $message = 'Test Log Message';
        $logger->log(LogLevel::ERROR, $message);

        rewind($resource);
        $logMessage = fread($resource, 1024);
        $this->assertStringStartsWith("\033[31m", $logMessage);
        $this->assertContains($message, $logMessage);
        $this->assertStringEndsWith("\033[39m\n", $logMessage);
    }

    public function testLogCritical()
    {
        $resource = fopen('php://memory', 'a');
        $logger = new CliColor('name', $resource);

        $message = 'Test Log Message';
        $logger->log(LogLevel::CRITICAL, $message);

        rewind($resource);
        $logMessage = fread($resource, 1024);
        $this->assertStringStartsWith("\033[31;43m", $logMessage);
        $this->assertContains($message, $logMessage);
        $this->assertStringEndsWith("\033[39;49m\n", $logMessage);
    }

    public function testLogAlert()
    {
        $resource = fopen('php://memory', 'a');
        $logger = new CliColor('name', $resource);

        $message = 'Test Log Message';
        $logger->log(LogLevel::ALERT, $message);

        rewind($resource);
        $logMessage = fread($resource, 1024);
        $this->assertStringStartsWith("\033[37;41;1m", $logMessage);
        $this->assertContains($message, $logMessage);
        $this->assertStringEndsWith("\033[39;49;22m\n", $logMessage);
    }

    public function testLogEmergency()
    {
        $resource = fopen('php://memory', 'a');
        $logger = new CliColor('name', $resource);

        $message = 'Test Log Message';
        $logger->log(LogLevel::EMERGENCY, $message);

        rewind($resource);
        $logMessage = fread($resource, 1024);
        $this->assertStringStartsWith("\033[37;41;1;4m", $logMessage);
        $this->assertContains($message, $logMessage);
        $this->assertStringEndsWith("\033[39;49;22;24m\n", $logMessage);
    }
}