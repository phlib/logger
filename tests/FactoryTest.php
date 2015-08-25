<?php

namespace Phlib\Logger;

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

    public function testCreateLoggerStreamUnfiltered()
    {
        $fh = fopen('php://memory', 'a');

        $factory = new Factory();
        $logger  = $factory->createLogger('test', [
            'name' => 'stream',
            'path' => $fh
        ]);

        $this->assertInstanceOf('\Phlib\Logger\Stream', $logger);
    }

    public function testCreateLoggerGelfUnfiltered()
    {
        $factory = new Factory();
        $logger  = $factory->createLogger('test', [
            'name' => 'gelf',
            'host' => '127.0.0.1'
        ]);

        $this->assertInstanceOf('\Gelf\Logger', $logger);
    }

    public function testCreateLoggerCollectionUnfiltered()
    {
        $factory = new Factory();
        $logger  = $factory->createLogger('test', [
            'name'    => 'collection',
            'loggers' => []
        ]);

        $this->assertInstanceOf('\Phlib\Logger\Collection', $logger);
    }

    public function testCreateLoggerStreamFiltered()
    {
        $fh = fopen('php://memory', 'a');

        $factory = new Factory();
        $logger  = $factory->createLogger('test', [
            'name'  => 'stream',
            'level' => LogLevel::ERROR,
            'path'  => $fh
        ]);

        $this->assertInstanceOf('\Phlib\Logger\LevelFilter', $logger);
    }

    /**
     * @expectedException \DomainException
     * @expectedExceptionMessage Logger config missing name
     */
    public function testCreateLoggerMissingLoggerName()
    {
        $factory = new Factory();
        $factory->createLogger('test', [ 'path' => 'filename' ]);
    }

    /**
     * @expectedException \DomainException
     * @expectedExceptionMessage Cannot find a logger named
     */
    public function testCreateLoggerInvalidLogger()
    {
        $factory = new Factory();
        $factory->createLogger('test', [ 'name' => 'unknown', 'path' => '(filename)' ]);
    }
}
