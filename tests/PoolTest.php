<?php

namespace Phlib\Logger\Test;

use Phlib\Logger\Pool;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class PoolTest extends TestCase
{

    public function testGetLogger()
    {
        $loggerConfig = [
            'type'    => 'collection',
            'loggers' => []
        ];

        $config  = $this->createMock(\Phlib\Logger\Config::class);
        $config->expects(static::once())
            ->method('getLoggerConfig')
            ->with(static::equalTo('test'))
            ->will(static::returnValue($loggerConfig));

        $factory = $this->createMock(\Phlib\Logger\Factory::class);
        $logger  = $this->createMock(\Phlib\Logger\LoggerType\Collection::class);
        $factory->expects(static::once())
            ->method('createLogger')
            ->with(static::equalTo('test'), static::equalTo($loggerConfig))
            ->will(static::returnValue($logger));

        $pool = new Pool($config, $factory);

        static::assertSame($logger, $pool->test);
    }

    public function testGetLoggerAgain()
    {
        $loggerConfig = [
            'type' => 'stream',
            'path' => '(filename)'
        ];

        $config  = $this->createMock(\Phlib\Logger\Config::class);
        $config->expects(static::once())
            ->method('getLoggerConfig')
            ->with(static::equalTo('test'))
            ->will(static::returnValue($loggerConfig));

        $factory = $this->createMock(\Phlib\Logger\Factory::class);
        $logger  = $this->createMock(\Phlib\Logger\LoggerType\Stream::class);
        $factory->expects(static::once())
            ->method('createLogger')
            ->with(static::equalTo('test'), static::equalTo($loggerConfig))
            ->will(static::returnValue($logger));

        $pool = new Pool($config, $factory);

        $actualLogger = $pool->test;

        static::assertSame($logger, $actualLogger);

        static::assertSame($actualLogger, $pool->test);
    }

    public function testPrefix()
    {
        $prefix = 'logger-prefix-';

        $loggerConfig = [
            'type'  => 'stream',
            'level' => LogLevel::CRITICAL,
            'path'  => '(filename)'
        ];

        $config  = $this->createMock(\Phlib\Logger\Config::class);
        $config->expects(static::once())
            ->method('getLoggerConfig')
            ->with(static::equalTo('test'))
            ->will(static::returnValue($loggerConfig));

        $factory = $this->createMock(\Phlib\Logger\Factory::class);
        $logger  = $this->createMock(\Phlib\Logger\LoggerType\Stream::class);
        $factory->expects(static::once())
            ->method('createLogger')
            ->with(static::equalTo($prefix . 'test'))
            ->will(static::returnValue($logger));

        $pool = new Pool($config, $factory);
        $pool->setPrefix($prefix);

        static::assertSame($logger, $pool->test);
    }

    public function testGetLoggerCollection()
    {
        $loggerConfig = [
            'type'  => 'stream',
            'level' => LogLevel::WARNING,
            'path'  => '(filename)'
        ];

        $config  = $this->createMock(\Phlib\Logger\Config::class);
        $config->expects(static::once())
            ->method('getLoggerConfig')
            ->with(static::equalTo('test'))
            ->will(static::returnValue($loggerConfig));

        $factory          = $this->createMock(\Phlib\Logger\Factory::class);
        $streamLogger     = $this->createMock(\Phlib\Logger\LoggerType\Stream::class);
        $collectionLogger = $this->createMock(\Phlib\Logger\LoggerType\Collection::class);
        $factory->expects(static::once())
            ->method('createLogger')
            ->with(
                static::equalTo('test'),
                static::equalTo($loggerConfig)
            )
            ->will(static::returnValue($streamLogger));
        $factory->expects(static::once())
            ->method('createCollectionLogger')
            ->with(
                static::equalTo('test'),
                static::equalTo([
                    'loggers' => [$streamLogger]
                ])
            )
            ->will(static::returnValue($collectionLogger));

        $pool = new Pool($config, $factory);

        static::assertSame($collectionLogger, $pool->getLoggerCollection('test'));
    }
}
