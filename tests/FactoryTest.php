<?php

declare(strict_types=1);

namespace Phlib\Logger\Test;

use Phlib\Logger\Factory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class FactoryTest extends TestCase
{
    public function testCreateStreamLogger(): void
    {
        $factory = new Factory();

        $fh = fopen('php://memory', 'a');
        $logger = $factory->createStreamLogger('test', [
            'path' => $fh,
        ]);

        static::assertInstanceOf(\Phlib\Logger\LoggerType\Stream::class, $logger);
    }

    public function testCreateGelfLogger(): void
    {
        $factory = new Factory();

        $logger = $factory->createGelfLogger('test', [
            'host' => '127.0.0.1',
        ]);

        static::assertInstanceOf(\Gelf\Logger::class, $logger);
    }

    public function testCreateCollectionLoggerEmpty(): void
    {
        $factory = new Factory();
        $logger = $factory->createCollectionLogger('test', [
            'loggers' => [],
        ]);

        static::assertInstanceOf(\Phlib\Logger\LoggerType\Collection::class, $logger);
    }

    public function testCreateCollectionLoggerExistingLogger(): void
    {
        $existingLogger = $this->createMock(\Psr\Log\LoggerInterface::class);

        $factory = new Factory();
        $logger = $factory->createCollectionLogger('test', [
            'loggers' => [$existingLogger],
        ]);

        static::assertInstanceOf(\Phlib\Logger\LoggerType\Collection::class, $logger);
    }

    public function testCreateCollectionLoggerWithConfig(): void
    {
        $gelfConfig = [
            'type' => 'gelf',
            'host' => '127.0.0.1',
        ];

        $factory = new Factory();
        $logger = $factory->createCollectionLogger('test', [
            'loggers' => [$gelfConfig],
        ]);

        static::assertInstanceOf(\Phlib\Logger\LoggerType\Collection::class, $logger);
    }

    public function testCreateCollectionLoggerWithInvalidConfig(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessageMatches('/at index 0$/');

        $invalidConfig = [
            'type' => '(invalid)',
        ];

        $factory = new Factory();
        $factory->createCollectionLogger('test', [
            'loggers' => [$invalidConfig],
        ]);
    }

    public function testCreateLoggerStreamUnfiltered(): void
    {
        $fh = fopen('php://memory', 'a');

        $factory = new Factory();
        $logger = $factory->createLogger('test', [
            'type' => 'stream',
            'path' => $fh,
        ]);

        static::assertInstanceOf(\Phlib\Logger\LoggerType\Stream::class, $logger);
    }

    public function testCreateLoggerGelfUnfiltered(): void
    {
        $factory = new Factory();
        $logger = $factory->createLogger('test', [
            'type' => 'gelf',
            'host' => '127.0.0.1',
        ]);

        static::assertInstanceOf(\Gelf\Logger::class, $logger);
    }

    public function testCreateLoggerCollectionUnfiltered(): void
    {
        $factory = new Factory();
        $logger = $factory->createLogger('test', [
            'type' => 'collection',
            'loggers' => [],
        ]);

        static::assertInstanceOf(\Phlib\Logger\LoggerType\Collection::class, $logger);
    }

    public function testDecoratorLevelIsRegisteredKey(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('key already in use');

        $configKey = 'level';

        $factory = new Factory();
        $factory->registerDecorator($configKey, 'dummy');
    }

    public function testDecoratorLevelIsRegisteredClass(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('class already registered');

        $className = \Phlib\Logger\Decorator\LevelFilter::class;

        $factory = new Factory();
        $factory->registerDecorator('dummy', $className);
    }

    public function testUnregisterDecorator(): void
    {
        $configKey = 'level';

        $factory = new Factory();
        $factory->unregisterDecorator($configKey);

        // If first unregister has succeeded, second call will throw exception
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('key not registered');

        $factory->unregisterDecorator($configKey);
    }

    public function testUnregisterDecoratorNotSet(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('key not registered');

        $configKey = 'dummy';

        $factory = new Factory();
        $factory->unregisterDecorator($configKey);
    }

    public function testCreateLoggerDecoratorNotRegistered(): void
    {
        $fh = fopen('php://memory', 'a');

        $factory = new Factory();
        $factory->unregisterDecorator('level');
        $logger = $factory->createLogger('test', [
            'type' => 'stream',
            'level' => LogLevel::ERROR,
            'path' => $fh,
        ]);

        static::assertNotInstanceOf(\Phlib\Logger\Decorator\LevelFilter::class, $logger);
        static::assertInstanceOf(\Phlib\Logger\LoggerType\Stream::class, $logger);
    }

    public function testCreateLoggerDecorator(): void
    {
        $fh = fopen('php://memory', 'a');

        $factory = new Factory();
        $factory->unregisterDecorator('level');
        $factory->registerDecorator('dummy', \Phlib\Logger\Decorator\LevelFilter::class);
        $logger = $factory->createLogger('test', [
            'type' => 'stream',
            'dummy' => LogLevel::ERROR,
            'path' => $fh,
        ]);

        static::assertInstanceOf(\Phlib\Logger\Decorator\LevelFilter::class, $logger);
    }

    public function testRegisterDecoratorMissing(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('class not found');

        $fh = fopen('php://memory', 'a');

        $factory = new Factory();
        $factory->registerDecorator('dummy', '\Phlib\Logger\not\a\class');
        $factory->createLogger('test', [
            'type' => 'stream',
            'dummy' => true,
            'path' => $fh,
        ]);
    }

    public function testRegisterDecoratorInvalid(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('class invalid');

        $fh = fopen('php://memory', 'a');

        $factory = new Factory();
        $factory->registerDecorator('dummy', \Phlib\Logger\Config::class);
        $logger = $factory->createLogger('test', [
            'type' => 'stream',
            'dummy' => true,
            'path' => $fh,
        ]);
    }

    public function testCreateLoggerMissingLoggerType(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Logger config missing logger type');

        $factory = new Factory();
        $factory->createLogger('test', [
            'path' => 'filename',
        ]);
    }

    public function testCreateLoggerInvalidLogger(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Cannot find a logger type named');

        $factory = new Factory();
        $factory->createLogger('test', [
            'type' => 'unknown',
            'path' => '(filename)',
        ]);
    }
}
