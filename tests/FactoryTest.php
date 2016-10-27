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

        $this->assertInstanceOf('\Phlib\Logger\LoggerType\Stream', $logger);
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

        $this->assertInstanceOf('\Phlib\Logger\LoggerType\Collection', $logger);
    }

    public function testCreateCollectionLoggerExistingLogger()
    {
        $existingLogger = $this->getMock('\Psr\Log\LoggerInterface');

        $factory = new Factory();
        $logger  = $factory->createCollectionLogger('test', [
            'loggers' => [$existingLogger]
        ]);

        $this->assertInstanceOf('\Phlib\Logger\LoggerType\Collection', $logger);
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

        $this->assertInstanceOf('\Phlib\Logger\LoggerType\Collection', $logger);
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

        $this->assertInstanceOf('\Phlib\Logger\LoggerType\Stream', $logger);
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

        $this->assertInstanceOf('\Phlib\Logger\LoggerType\Collection', $logger);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage key already in use
     */
    public function testDecoratorLevelIsRegisteredKey()
    {
        $configKey = 'level';

        $factory = new Factory();
        $factory->registerDecorator($configKey, 'dummy');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage class already registered
     */
    public function testDecoratorLevelIsRegisteredClass()
    {
        $className = '\Phlib\Logger\Decorator\LevelFilter';

        $factory = new Factory();
        $factory->registerDecorator('dummy', $className);
    }

    public function testUnregisterDecorator()
    {
        $configKey = 'level';

        $factory = new Factory();
        $factory->unregisterDecorator($configKey);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage key not registered
     */
    public function testUnregisterDecoratorNotSet()
    {
        $configKey = 'dummy';

        $factory = new Factory();
        $factory->unregisterDecorator($configKey);
    }

    public function testCreateLoggerDecoratorNotRegistered()
    {
        $fh = fopen('php://memory', 'a');

        $factory = new Factory();
        $factory->unregisterDecorator('level');
        $logger  = $factory->createLogger('test', [
            'type'   => 'stream',
            'level' => LogLevel::ERROR,
            'path'  => $fh
        ]);

        $this->assertNotInstanceOf('\Phlib\Logger\Decorator\LevelFilter', $logger);
        $this->assertInstanceOf('\Phlib\Logger\LoggerType\Stream', $logger);
    }

    public function testCreateLoggerDecorator()
    {
        $fh = fopen('php://memory', 'a');

        $factory = new Factory();
        $factory->unregisterDecorator('level');
        $factory->registerDecorator('dummy', '\Phlib\Logger\Decorator\LevelFilter');
        $logger  = $factory->createLogger('test', [
            'type'   => 'stream',
            'dummy' => LogLevel::ERROR,
            'path'  => $fh
        ]);

        $this->assertInstanceOf('\Phlib\Logger\Decorator\LevelFilter', $logger);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage class not found
     */
    public function testRegisterDecoratorMissing()
    {
        $fh = fopen('php://memory', 'a');

        $factory = new Factory();
        $factory->registerDecorator('dummy', '\Phlib\Logger\not\a\class');
        $factory->createLogger('test', [
            'type'  => 'stream',
            'dummy' => true,
            'path'  => $fh
        ]);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage class invalid
     */
    public function testRegisterDecoratorInvalid()
    {
        $fh = fopen('php://memory', 'a');

        $factory = new Factory();
        $factory->registerDecorator('dummy', '\Phlib\Logger\Config');
        $logger  = $factory->createLogger('test', [
            'type'  => 'stream',
            'dummy' => true,
            'path'  => $fh
        ]);
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
