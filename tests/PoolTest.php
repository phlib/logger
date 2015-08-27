<?php

namespace Phlib\Logger\Test;

use Phlib\Logger\Factory;
use Phlib\Logger\Pool;
use Psr\Log\LogLevel;

class PoolTest extends \PHPUnit_Framework_TestCase
{

    public function testGetLogger()
    {
        $loggerConfig = [
            'name'    => Factory::LOGGER_COLLECTION,
            'loggers' => []
        ];

        $config  = $this->getMock('\Phlib\Logger\Config', [], [[]]);
        $config->expects($this->once())
            ->method('getLoggerConfig')
            ->with($this->equalTo('test'))
            ->will($this->returnValue($loggerConfig));

        $factory = $this->getMock('\Phlib\Logger\Factory');
        $logger  = $this->getMock('\Phlib\Logger\Collection');
        $factory->expects($this->once())
            ->method('createLogger')
            ->with($this->equalTo('test'), $this->equalTo($loggerConfig))
            ->will($this->returnValue($logger));

        $pool = new Pool($config, $factory);

        $this->assertSame($logger, $pool->test);
    }

    public function testGetLoggerAgain()
    {
        $loggerConfig = [
            'name' => Factory::LOGGER_STREAM,
            'path' => '(filename)'
        ];

        $config  = $this->getMock('\Phlib\Logger\Config', [], [[]]);
        $config->expects($this->once())
            ->method('getLoggerConfig')
            ->with($this->equalTo('test'))
            ->will($this->returnValue($loggerConfig));

        $factory = $this->getMock('\Phlib\Logger\Factory');
        $logger  = $this->getMockBuilder('\Phlib\Logger\Stream')->disableOriginalConstructor()->getMock();
        $factory->expects($this->once())
            ->method('createLogger')
            ->with($this->equalTo('test'), $this->equalTo($loggerConfig))
            ->will($this->returnValue($logger));

        $pool = new Pool($config, $factory);

        $actualLogger = $pool->test;

        $this->assertSame($logger, $actualLogger);

        $this->assertSame($actualLogger, $pool->test);
    }

    public function testPrefix()
    {
        $prefix = 'logger-prefix-';

        $loggerConfig = [
            'name'  => Factory::LOGGER_GELF,
            'level' => LogLevel::CRITICAL,
            'host'  => '(hostname)'
        ];

        $config  = $this->getMock('\Phlib\Logger\Config', [], [[]]);
        $config->expects($this->once())
            ->method('getLoggerConfig')
            ->with($this->equalTo('test'))
            ->will($this->returnValue($loggerConfig));

        $factory = $this->getMock('\Phlib\Logger\Factory');
        $logger  = $this->getMock('\Phlib\Logger\Gelf');
        $factory->expects($this->once())
            ->method('createLogger')
            ->with($this->equalTo($prefix . 'test'))
            ->will($this->returnValue($logger));

        $pool = new Pool($config, $factory);
        $pool->setPrefix($prefix);

        $this->assertSame($logger, $pool->test);
    }

    public function testGetLoggerCollection()
    {
        $loggerConfig = [
            'name'  => Factory::LOGGER_STREAM,
            'level' => LogLevel::WARNING,
            'path'  => '(filename)'
        ];

        $config  = $this->getMock('\Phlib\Logger\Config', [], [[]]);
        $config->expects($this->once())
            ->method('getLoggerConfig')
            ->with($this->equalTo('test'))
            ->will($this->returnValue($loggerConfig));

        $factory          = $this->getMock('\Phlib\Logger\Factory');
        $streamLogger     = $this->getMockBuilder('\Phlib\Logger\Stream')->disableOriginalConstructor()->getMock();
        $collectionLogger = $this->getMock('\Phlib\Logger\Collection');
        $factory->expects($this->once())
            ->method('createLogger')
            ->with(
                $this->equalTo('test'),
                $this->equalTo($loggerConfig)
            )
            ->will($this->returnValue($streamLogger));
        $factory->expects($this->once())
            ->method('createCollectionLogger')
            ->with(
                $this->equalTo('test'),
                $this->equalTo([
                    'loggers' => [$streamLogger]
                ])
            )
            ->will($this->returnValue($collectionLogger));

        $pool = new Pool($config, $factory);

        $this->assertSame($collectionLogger, $pool->getLoggerCollection('test'));
    }
}
