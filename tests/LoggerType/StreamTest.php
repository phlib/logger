<?php

declare(strict_types=1);

namespace Phlib\Logger\Test\LoggerType;

use Phlib\Logger\LoggerType\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class StreamTest extends TestCase
{
    public function testIsPsrLog(): void
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        static::assertInstanceOf(\Psr\Log\LoggerInterface::class, $stream);
    }

    public function testLog(): void
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $message = 'Test Log Message';
        $stream->log(LogLevel::ALERT, $message);

        rewind($resource);
        $logMessage = fgets($resource);
        static::assertStringContainsString($message, $logMessage);
    }

    public function testMessageFormatName(): void
    {
        $streamName = 'myTestStreamLogger';
        $resource = fopen('php://memory', 'a');
        $stream = new Stream($streamName, $resource);

        $stream->setMessageFormat('{name}');
        $stream->log(LogLevel::ALERT, 'Test Log Message');

        rewind($resource);
        $logMessage = fgets($resource);

        static::assertEquals($streamName . PHP_EOL, $logMessage);
    }

    public function testMessageFormatLevel(): void
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setMessageFormat('{level}');
        $level = LogLevel::ALERT;
        $stream->log($level, 'Test Log Message');

        rewind($resource);
        $logMessage = fgets($resource);
        static::assertEquals($level . PHP_EOL, $logMessage);
    }

    public function testMessageFormatMessage(): void
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setMessageFormat('{message}');
        $message = 'Test Log Message';
        $stream->log(LogLevel::ALERT, $message);

        rewind($resource);
        $logMessage = fgets($resource);
        static::assertEquals($message . PHP_EOL, $logMessage);
    }

    public function testMessageFormatContext(): void
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $contextException = new \Exception('Test Exception Context');
        $context = [
            'field1' => 'Test Field 1',
            'field2' => 'Test Field 2',
            'field3' => 'Test Field 3',
            'exception' => $contextException,
        ];

        $stream->setMessageFormat('{context}');
        $stream->log(LogLevel::ALERT, 'Test Log Message', $context);

        rewind($resource);
        $logMessage = fgets($resource);

        $context['exception'] = (string)$contextException;
        $contextString = json_encode($context, JSON_UNESCAPED_SLASHES);

        static::assertEquals($contextString . PHP_EOL, $logMessage);
    }

    public function testNewDateFormat(): void
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setDateFormat('d/m/Y');
        $stream->setMessageFormat('{datetime}');

        $stream->log(LogLevel::ALERT, 'Test Log Message');

        rewind($resource);
        $logMessage = fgets($resource);

        static::assertStringMatchesFormat('%d/%d/%d' . PHP_EOL, $logMessage);
    }

    public function testStringCannotOpenException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to open');

        new Stream('name', __DIR__ . '/path/what/does/not/exist');
    }

    public function testFormatContextBoolean(): void
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setMessageFormat('{message}');

        $stream->log(LogLevel::ALERT, '{myvalue}', [
            'myvalue' => true,
        ]);

        rewind($resource);
        $logMessage = fgets($resource);

        static::assertEquals('true' . PHP_EOL, $logMessage);
    }

    public function testFormatContextString(): void
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setMessageFormat('{message}');

        $stream->log(LogLevel::ALERT, '{myvalue}', [
            'myvalue' => 'value',
        ]);

        rewind($resource);
        $logMessage = fgets($resource);

        static::assertEquals('value' . PHP_EOL, $logMessage);
    }

    public function testFormatContextNull(): void
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setMessageFormat('{message}');

        $stream->log(LogLevel::ALERT, '{myvalue}', [
            'myvalue' => null,
        ]);

        rewind($resource);
        $logMessage = fgets($resource);

        static::assertEquals('NULL' . PHP_EOL, $logMessage);
    }

    public function testFormatContextClass(): void
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setMessageFormat('{message}');

        $object = new \stdClass();

        $stream->log(LogLevel::ALERT, '{myvalue}', [
            'myvalue' => $object,
        ]);

        rewind($resource);
        $logMessage = fgets($resource);

        static::assertEquals(\stdClass::class . PHP_EOL, $logMessage);
    }

    public function testFormatContextRawType(): void
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setMessageFormat('{message}');

        $stream->log(LogLevel::ALERT, '{myvalue}', [
            'myvalue' => [],
        ]);

        rewind($resource);
        $logMessage = fgets($resource);

        static::assertEquals('array' . PHP_EOL, $logMessage);
    }
}
