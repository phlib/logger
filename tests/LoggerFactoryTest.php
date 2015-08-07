<?php
namespace Phlib\Logger\Test;

use Phlib\Logger\LoggerFactory;
use Psr\Log\LogLevel;

class LoggerFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testEmptyLogger()
    {
        $factory = new LoggerFactory([]);
        $logger = $factory->test;

        $this->assertInstanceOf('\Phlib\Logger\Collection', $logger);
    }

    public function testStream()
    {
        $fh = fopen('php://memory', 'a');

        $streamConfig = [
            'name' => 'stream',
            'path' => $fh
        ];

        $config = [
            'test' => [
                $streamConfig
            ]
        ];

        $factory = new LoggerFactory($config);
        $factory->test->alert('Hello world');

        rewind($fh);
        $this->assertContains('Hello world', fgets($fh));
    }

    public function testStreamMulti()
    {
        $fhs = [
            fopen('php://memory', 'a'),
            fopen('php://memory', 'a'),
            fopen('php://memory', 'a'),
        ];

        $streamConfig1 = [
            'name'  => 'stream',
            'path'  => $fhs[0]
        ];

        $streamConfig2 = [
            'name'  => 'stream',
            'path'  => $fhs[1]
        ];

        $streamConfig3 = [
            'name'  => 'stream',
            'path'  => $fhs[2]
        ];

        $config = [
            'test' => [
                $streamConfig1,
                $streamConfig2,
                $streamConfig3,
            ]
        ];

        $factory = new LoggerFactory($config);
        $factory->test->debug('Hello world');

        foreach ($fhs as $fh) {
            rewind($fh);
            $this->assertContains('Hello world', fgets($fh));
        }
    }

    public function testFilteredStream()
    {
        $fh1 = fopen('php://memory', 'a');
        $fh2 = fopen('php://memory', 'a');

        $streamConfig1 = [
            'name'  => 'stream',
            'level' => LogLevel::CRITICAL,
            'path'  => $fh1
        ];

        $streamConfig2 = [
            'name'  => 'stream',
            'level' => LogLevel::NOTICE,
            'path'  => $fh2
        ];

        $config = [
            'test' => [
                $streamConfig1,
                $streamConfig2
            ]
        ];

        $factory = new LoggerFactory($config);
        $factory->test->error('Hello world');

        rewind($fh1);
        $this->assertEmpty(fgets($fh1));

        rewind($fh2);
        $this->assertContains('Hello world', fgets($fh2));
    }

    public function testPrefix()
    {
        $testConfig = [
            [
                'name'  => 'stream',
                'level' => LogLevel::CRITICAL,
                'path'  => '(filename)'
            ]
        ];
        $config = [
            'test' => $testConfig
        ];

        $factory = \Mockery::mock('\Phlib\Logger\LoggerFactory', [$config])->makePartial();
        $factory->setPrefix('logger-prefix-');

        $logger = new \stdClass();
        $factory
            ->shouldReceive('createLogger')
            ->with('logger-prefix-test', $testConfig)
            ->andReturn($logger)
        ;

        $this->assertSame($logger, $factory->getLogger('test'));
    }

    public function testAlias()
    {
        $testConfig = [
            [
                'name'  => 'stream',
                'level' => LogLevel::CRITICAL,
                'path'  => '(filename)'
            ]
        ];
        $config = [
            'test'  => 'test1',
            'test1' => 'test2',
            'test2' => $testConfig
        ];

        $factory = \Mockery::mock('\Phlib\Logger\LoggerFactory', [$config])->makePartial();

        $logger = new \stdClass();
        $factory
            ->shouldReceive('createLogger')
            ->with('test', $testConfig)
            ->andReturn($logger)
        ;

        $this->assertSame($logger, $factory->getLogger('test'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Logger config missing name
     */
    public function testMissingLoggerName()
    {
        $config = [
            'test'  => [
                [
                    'level' => LogLevel::CRITICAL,
                    'path'  => '(filename)'
                ]
            ]
        ];

        $factory = new LoggerFactory($config);
        $factory->test->alert('Hello world');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Cannot find a logger named
     */
    public function testInvalidLogger()
    {
        $config = [
            'test'  => [
                [
                    'name'  => 'unknown',
                    'level' => LogLevel::CRITICAL,
                    'path'  => '(filename)'
                ]
            ]
        ];

        $factory = new LoggerFactory($config);
        $factory->test->alert('Hello world');
    }
}
