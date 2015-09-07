<?php

namespace Phlib\Logger\Test;

use Phlib\Logger\Config;
use Phlib\Logger\Factory;
use Psr\Log\LogLevel;

class ConfigTest extends \PHPUnit_Framework_TestCase
{

    public function testEmptyConfig()
    {
        $configArray = [];
        $config = new Config($configArray);

        $expected = [
            'type'    => Factory::LOGGER_TYPE_COLLECTION,
            'loggers' => []
        ];

        $this->assertEquals($expected, $config->getLoggerConfig('test'));
    }

    public function testStream()
    {
        $streamConfig = [
            'type' => Factory::LOGGER_TYPE_STREAM,
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
            'type' => Factory::LOGGER_TYPE_GELF,
        ];

        $configArray = [
            'test' => [
                $gelfConfig
            ]
        ];

        $config = new Config($configArray);

        $expected = [
            'type'    => Factory::LOGGER_TYPE_COLLECTION,
            'loggers' => [
                $gelfConfig
            ]
        ];

        $this->assertEquals($expected, $config->getLoggerConfig('test'));
    }

    public function testAlias()
    {
        $streamConfig = [
            'type'  => Factory::LOGGER_TYPE_STREAM,
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
            'type'    => Factory::LOGGER_TYPE_COLLECTION,
            'loggers' => []
        ];

        $this->assertEquals($expected, $config->getLoggerConfig('test'));
    }
}
