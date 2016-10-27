<?php

namespace Phlib\Logger\Test;

use Phlib\Logger\Pool;
use Psr\Log\LogLevel;

class PoolTest extends \PHPUnit_Framework_TestCase
{

    public function testGetLogger()
    {
        $loggerConfig = [
            'type'    => 'collection',
            'loggers' => []
        ];

        $config  = $this->getMock('\Phlib\Logger\Config', [], [[]]);
        $config->expects($this->once())
            ->method('getLoggerConfig')
            ->with($this->equalTo('test'))
            ->will($this->returnValue($loggerConfig));

        $factory = $this->getMock('\Phlib\Logger\Factory');
        $logger  = $this->getMock('\Phlib\Logger\LoggerType\Collection');
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
            'type' => 'stream',
            'path' => '(filename)'
        ];

        $config  = $this->getMock('\Phlib\Logger\Config', [], [[]]);
        $config->expects($this->once())
            ->method('getLoggerConfig')
            ->with($this->equalTo('test'))
            ->will($this->returnValue($loggerConfig));

        $factory = $this->getMock('\Phlib\Logger\Factory');
        $logger  = $this->getMockBuilder('\Phlib\Logger\LoggerType\Stream')->disableOriginalConstructor()->getMock();
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
            'type'  => 'stream',
            'level' => LogLevel::CRITICAL,
            'path'  => '(filename)'
        ];

        $config  = $this->getMock('\Phlib\Logger\Config', [], [[]]);
        $config->expects($this->once())
            ->method('getLoggerConfig')
            ->with($this->equalTo('test'))
            ->will($this->returnValue($loggerConfig));

        $factory = $this->getMock('\Phlib\Logger\Factory');
        $logger  = $this->getMockBuilder('\Phlib\Logger\LoggerType\Stream')->disableOriginalConstructor()->getMock();
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
            'type'  => 'stream',
            'level' => LogLevel::WARNING,
            'path'  => '(filename)'
        ];

        $config  = $this->getMock('\Phlib\Logger\Config', [], [[]]);
        $config->expects($this->once())
            ->method('getLoggerConfig')
            ->with($this->equalTo('test'))
            ->will($this->returnValue($loggerConfig));

        $factory          = $this->getMock('\Phlib\Logger\Factory');
        $streamLogger     = $this->getMockBuilder('\Phlib\Logger\LoggerType\Stream')->disableOriginalConstructor()->getMock();
        $collectionLogger = $this->getMock('\Phlib\Logger\LoggerType\Collection');
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
