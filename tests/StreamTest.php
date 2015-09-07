<?php
namespace Phlib\Logger\Test;

use Phlib\Logger\Stream;
use Psr\Log\LogLevel;

class StreamTest extends \PHPUnit_Framework_TestCase
{

    public function testIsPsrLog()
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $this->assertInstanceOf('\Psr\Log\LoggerInterface', $stream);
    }

    public function testLog()
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $message = 'Test Log Message';
        $stream->log(LogLevel::ALERT, $message);

        rewind($resource);
        $logMessage = fgets($resource);
        $this->assertContains($message, $logMessage);
    }

    public function testMessageFormatName()
    {
        $streamName = 'myTestStreamLogger';
        $resource = fopen('php://memory', 'a');
        $stream = new Stream($streamName, $resource);

        $stream->setMessageFormat('{name}');
        $stream->log(LogLevel::ALERT, 'Test Log Message');

        rewind($resource);
        $logMessage = fgets($resource);

        $this->assertEquals($streamName, $logMessage);
    }

    public function testMessageFormatLevel()
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setMessageFormat('{level}');
        $level = LogLevel::ALERT;
        $stream->log($level, 'Test Log Message');

        rewind($resource);
        $logMessage = fgets($resource);
        $this->assertEquals($level, $logMessage);
    }

    public function testMessageFormatMessage()
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setMessageFormat('{message}');
        $message = 'Test Log Message';
        $stream->log(LogLevel::ALERT, $message);

        rewind($resource);
        $logMessage = fgets($resource);
        $this->assertEquals($message, $logMessage);
    }

    public function testMessageFormatContext()
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $contextException = new \Exception('Test Exception Context');
        $context = [
            'field1' => 'Test Field 1',
            'field2' => 'Test Field 2',
            'field3' => 'Test Field 3',
            'exception' => $contextException
        ];

        $stream->setMessageFormat('{context}');
        $stream->log(LogLevel::ALERT, 'Test Log Message', $context);

        rewind($resource);
        $logMessage = fgets($resource);

        $context['exception'] = (string)$contextException;
        $contextString = json_encode($context, JSON_UNESCAPED_SLASHES);

        $this->assertEquals($contextString, $logMessage);
    }

    public function testNewDateFormat()
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setDateFormat('d/m/Y');
        $stream->setMessageFormat('{datetime}');

        $stream->log(LogLevel::ALERT, 'Test Log Message');

        rewind($resource);
        $logMessage = fgets($resource);

        $this->assertStringMatchesFormat('%d/%d/%d', $logMessage);
    }

    public function testStringResource()
    {
        $stream = new Stream('name', 'php://memory');

        $this->assertAttributeInternalType('resource', 'stream', $stream);
    }

    public function testFormatContextBoolean()
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setMessageFormat('{message}');

        $stream->log(LogLevel::ALERT, '{myvalue}', [
            'myvalue' => true
        ]);

        rewind($resource);
        $logMessage = fgets($resource);

        $this->assertEquals('true', $logMessage);
    }

    public function testFormatContextString()
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setMessageFormat('{message}');

        $stream->log(LogLevel::ALERT, '{myvalue}', [
            'myvalue' => 'value'
        ]);

        rewind($resource);
        $logMessage = fgets($resource);

        $this->assertEquals('value', $logMessage);
    }

    public function testFormatContextNull()
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setMessageFormat('{message}');

        $stream->log(LogLevel::ALERT, '{myvalue}', [
            'myvalue' => null
        ]);

        rewind($resource);
        $logMessage = fgets($resource);

        $this->assertEquals('NULL', $logMessage);
    }

    public function testFormatContextClass()
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setMessageFormat('{message}');

        $object = new \stdClass();

        $stream->log(LogLevel::ALERT, '{myvalue}', [
            'myvalue' => $object
        ]);

        rewind($resource);
        $logMessage = fgets($resource);

        $this->assertEquals('stdClass', $logMessage);
    }

    public function testFormatContextRawType()
    {
        $resource = fopen('php://memory', 'a');
        $stream = new Stream('name', $resource);

        $stream->setMessageFormat('{message}');

        $stream->log(LogLevel::ALERT, '{myvalue}', [
            'myvalue' => []
        ]);

        rewind($resource);
        $logMessage = fgets($resource);

        $this->assertEquals('array', $logMessage);
    }
}
