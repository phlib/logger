<?php

namespace Phlib\Logger\Test;

use Phlib\Logger\Factory;
use Psr\Log\LogLevel;

class FactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateStreamLogger()
    {
        $factory = new Factory();

        $fh = fopen('php://memory', 'a');
        $logger = $factory->createStreamLogger('test', [ 'path' => $fh ]);

        $this->assertInstanceOf('\Phlib\Logger\Stream', $logger);
    }

    public function testCreateGelfLogger()
    {
        $factory = new Factory();

        $logger = $factory->createGelfLogger('test', [ 'host' => '127.0.0.1' ]);

        $this->assertInstanceOf('\Gelf\Logger', $logger);
    }

    public function testCreateCollectionLoggerEmpty()
    {
        $factory = new Factory();
        $logger  = $factory->createCollectionLogger('test', [
            'loggers' => []
        ]);

        $this->assertInstanceOf('\Phlib\Logger\Collection', $logger);
    }

    public function testCreateCollectionLoggerExistingLogger()
    {
        $existingLogger = $this->getMock('\Psr\Log\LoggerInterface');

        $factory = new Factory();
        $logger  = $factory->createCollectionLogger('test', [
            'loggers' => [$existingLogger]
        ]);

        $this->assertInstanceOf('\Phlib\Logger\Collection', $logger);
    }

    public function testCreateCollectionLoggerWithConfig()
    {
        $gelfConfig = [
            'type' => 'gelf',
            'host' => '127.0.0.1'
        ];

        $factory = new Factory();
        $logger  = $factory->createCollectionLogger('test', [
            'loggers' => [$gelfConfig]
        ]);

        $this->assertInstanceOf('\Phlib\Logger\Collection', $logger);
    }

    /**
     * @expectedException \DomainException
     * @expectedExceptionMessageRegExp /at index 0$/
     */
    public function testCreateCollectionLoggerWithInvalidConfig()
    {
        $invalidConfig = [
            'type' => '(invalid)',
        ];

        $factory = new Factory();
        $factory->createCollectionLogger('test', [
            'loggers' => [$invalidConfig]
        ]);
    }

    public function testCreateLoggerStreamUnfiltered()
    {
        $fh = fopen('php://memory', 'a');

        $factory = new Factory();
        $logger  = $factory->createLogger('test', [
            'type' => 'stream',
            'path' => $fh
        ]);

        $this->assertInstanceOf('\Phlib\Logger\Stream', $logger);
    }

    public function testCreateLoggerGelfUnfiltered()
    {
        $factory = new Factory();
        $logger  = $factory->createLogger('test', [
            'type' => 'gelf',
            'host' => '127.0.0.1'
        ]);

        $this->assertInstanceOf('\Gelf\Logger', $logger);
    }

    public function testCreateLoggerCollectionUnfiltered()
    {
        $factory = new Factory();
        $logger  = $factory->createLogger('test', [
            'type'    => 'collection',
            'loggers' => []
        ]);

        $this->assertInstanceOf('\Phlib\Logger\Collection', $logger);
    }

    public function testCreateLoggerStreamFiltered()
    {
        $fh = fopen('php://memory', 'a');

        $factory = new Factory();
        $logger  = $factory->createLogger('test', [
            'type'   => 'stream',
            'level' => LogLevel::ERROR,
            'path'  => $fh
        ]);

        $this->assertInstanceOf('\Phlib\Logger\Decorator\LevelFilter', $logger);
    }

    /**
     * @expectedException \DomainException
     * @expectedExceptionMessage Logger config missing logger type
     */
    public function testCreateLoggerMissingLoggerType()
    {
        $factory = new Factory();
        $factory->createLogger('test', [ 'path' => 'filename' ]);
    }

    /**
     * @expectedException \DomainException
     * @expectedExceptionMessage Cannot find a logger type named
     */
    public function testCreateLoggerInvalidLogger()
    {
        $factory = new Factory();
        $factory->createLogger('test', [ 'type' => 'unknown', 'path' => '(filename)' ]);
    }
}
