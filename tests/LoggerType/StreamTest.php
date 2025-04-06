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

    public function testFormatContextBinary(): void
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setMessageFormat('{context}');

        // This string as binary will fail without option JSON_INVALID_UTF8_SUBSTITUTE
        $hex = '16030100b3010000af0302a17475a24e43d715923c81e27d02d6715d4b960e7c2d7891aca6ab8229be537c000038c00ac014' .
            '0039003800880087c019003a0089c009c0130033003200450044c0180034004600350084002f0041c006c010c01500020001' .
            '00ff0100004e0000002600240000216d6178656d61696c2d636d2e6465762e656d61696c63656e746572756b2e636f6d000b' .
            '000403000102000a000c000a001d0017001e00190018002300000016000000170000';

        $stream->log(LogLevel::ALERT, 'test', [
            'bin' => hex2bin($hex),
        ]);

        rewind($resource);
        $logMessage = fgets($resource);

        $expected = "\u0016\u0003\u0001\u0000\ufffd\u0001\u0000\u0000\ufffd\u0003\u0002\ufffdtu\ufffdNC\ufffd\u0015" .
            "\ufffd<\ufffd\ufffd}\u0002\ufffdq]K\ufffd\u000e|-x\ufffd\ufffd\ufffd\ufffd\ufffd)\ufffdS|\u0000\u00008" .
            "\ufffd\\n\ufffd\u0014\u00009\u00008\u0000\ufffd\u0000\ufffd\ufffd\u0019\u0000:\u0000\ufffd\ufffd\\t\ufffd" .
            "\u0013\u00003\u00002\u0000E\u0000D\ufffd\u0018\u00004\u0000F\u00005\u0000\ufffd\u0000/\u0000A\ufffd" .
            "\u0006\ufffd\u0010\ufffd\u0015\u0000\u0002\u0000\u0001\u0000\ufffd\u0001\u0000\u0000N\u0000\u0000\u0000&" .
            "\u0000$\u0000\u0000!maxemail-cm.dev.emailcenteruk.com\u0000\u000b\u0000\u0004\u0003\u0000\u0001\u0002" .
            "\u0000\\n\u0000\\f\u0000\\n\u0000\u001d\u0000\u0017\u0000\u001e\u0000\u0019\u0000\u0018\u0000#\u0000\u0000" .
            "\u0000\u0016\u0000\u0000\u0000\u0017\u0000\u0000";

        static::assertEquals(
            '{"bin":"' . $expected . '"}' . PHP_EOL,
            $logMessage,
        );
    }

    public function testFormatContextCannotJsonEncode(): void
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setMessageFormat('{context}');

        $stream->log(LogLevel::ALERT, 'test', [
            'resource' => $resource,
        ]);

        rewind($resource);
        $logMessage = fgets($resource);

        static::assertEquals(
            'Unable to format context: Type is not supported' . PHP_EOL,
            $logMessage,
        );
    }
}
