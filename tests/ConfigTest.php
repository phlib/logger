<?php

namespace Phlib\Logger\Test;

use Phlib\Logger\Config;
use Phlib\Logger\Factory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class ConfigTest extends TestCase
{

    public function testEmptyConfig()
    {
        $configArray = [];
        $config = new Config($configArray);

        $expected = [
            'type'    => 'collection',
            'loggers' => []
        ];

        $this->assertEquals($expected, $config->getLoggerConfig('test'));
    }

    public function testStream()
    {
        $streamConfig = [
            'type' => 'stream',
            'path' => '(filename)'
        ];

        $configArray = [
            'test' => $streamConfig
        ];

        $config = new Config($configArray);

        $this->assertEquals($streamConfig, $config->getLoggerConfig('test'));
    }

    public function testCollectionCoerce()
    {
        $gelfConfig = [
            'type' => 'gelf',
        ];

        $configArray = [
            'test' => [
                $gelfConfig
            ]
        ];

        $config = new Config($configArray);

        $expected = [
            'type'    => 'collection',
            'loggers' => [
                $gelfConfig
            ]
        ];

        $this->assertEquals($expected, $config->getLoggerConfig('test'));
    }

    public function testAlias()
    {
        $streamConfig = [
            'type'  => 'stream',
            'level' => LogLevel::CRITICAL,
            'path'  => '(filename)'
        ];

        $configArray = [
            'test'  => 'test1',
            'test1' => 'test2',
            'test2' => $streamConfig
        ];

        $config = new Config($configArray);

        $this->assertEquals($streamConfig, $config->getLoggerConfig('test'));
    }

    public function testInvalidLoggerConfigType()
    {
        $configArray = [
            'test' => false
        ];

        $config = new Config($configArray);

        $expected = [
            'type'    => 'collection',
            'loggers' => []
        ];

        $this->assertEquals($expected, $config->getLoggerConfig('test'));
    }
}
