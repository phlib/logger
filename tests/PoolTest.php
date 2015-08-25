<?php

namespace Phlib\Logger\Test;

use Phlib\Logger\Pool;
use Psr\Log\LogLevel;

class PoolTest extends \PHPUnit_Framework_TestCase
{

    public function testEmptyLogger()
    {
        $factory = $this->getMock('\Phlib\Logger\Factory');
        $logger  = $this->getMock('\Phlib\Logger\Collection');
        $factory->expects($this->once())
            ->method('createLogger')
            ->with(
                $this->equalTo('test'),
                $this->equalTo([
                    'name'    => 'collection',
                    'loggers' => []
                ])
            )
            ->will($this->returnValue($logger));

        $pool = new Pool([], $factory);

        $this->assertSame($logger, $pool->test);
    }

    public function testStream()
    {
        $streamConfig = [
            'name' => 'stream',
        ];

        $config = [
            'test' => $streamConfig
        ];

        $factory = $this->getMock('\Phlib\Logger\Factory');
        $logger  = $this->getMockBuilder('\Phlib\Logger\Stream')->disableOriginalConstructor()->getMock();
        $factory->expects($this->once())
            ->method('createLogger')
            ->with(
                $this->equalTo('test'),
                $this->equalTo($streamConfig)
            )
            ->will($this->returnValue($logger));
        $pool = new Pool($config, $factory);

        $this->assertSame($logger, $pool->test);
    }

    public function testCollectionStream()
    {
        $streamConfig = [
            'name' => 'stream',
        ];

        $config = [
            'test' => [
                $streamConfig
            ]
        ];

        $factory = $this->getMock('\Phlib\Logger\Factory');
        $logger  = $this->getMock('\Phlib\Logger\Collection');
        $factory->expects($this->once())
            ->method('createLogger')
            ->with(
                $this->equalTo('test'),
                $this->equalTo([
                    'name'    => 'collection',
                    'loggers' => [$streamConfig]
                ])
            )
            ->will($this->returnValue($logger));
        $pool = new Pool($config, $factory);

        $this->assertSame($logger, $pool->test);
    }

    public function testPrefix()
    {
        $config = [
            'test' => [
                [
                    'name'  => 'stream',
                    'level' => LogLevel::CRITICAL,
                    'path'  => '(filename)'
                ]
            ]
        ];

        $factory = $this->getMock('\Phlib\Logger\Factory');
        $logger  = $this->getMock('\Phlib\Logger\Collection');
        $factory->expects($this->once())
            ->method('createLogger')
            ->with($this->equalTo('logger-prefix-test'))
            ->will($this->returnValue($logger));

        $pool = new Pool($config, $factory);
        $pool->setPrefix('logger-prefix-');

        $this->assertSame($logger, $pool->test);
    }

    public function testAlias()
    {
        $loggers = [
            [
                'name'  => 'stream',
                'level' => LogLevel::CRITICAL,
                'path'  => '(filename)'
            ]
        ];
        $config = [
            'test'  => 'test1',
            'test1' => 'test2',
            'test2' => $loggers
        ];


        $factory = $this->getMock('\Phlib\Logger\Factory');
        $logger  = $this->getMock('\Phlib\Logger\Collection');
        $factory->expects($this->once())
            ->method('createLogger')
            ->with(
                $this->equalTo('test'),
                $this->equalTo([
                    'name'    => 'collection',
                    'loggers' => $loggers
                ])
            )
            ->will($this->returnValue($logger));

        $pool = new Pool($config, $factory);

        $this->assertSame($logger, $pool->getLogger('test'));
    }
}
