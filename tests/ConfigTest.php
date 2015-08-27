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
            'name'    => Factory::LOGGER_COLLECTION,
            'loggers' => []
        ];

        $this->assertEquals($expected, $config->test);
    }

    public function testStream()
    {
        $streamConfig = [
            'name' => Factory::LOGGER_STREAM,
            'path' => '(filename)'
        ];

        $configArray = [
            'test' => $streamConfig
        ];

        $config = new Config($configArray);

        $this->assertEquals($streamConfig, $config->test);
    }

    public function testCollectionCoerce()
    {
        $gelfConfig = [
            'name' => Factory::LOGGER_GELF,
        ];

        $configArray = [
            'test' => [
                $gelfConfig
            ]
        ];

        $config = new Config($configArray);

        $expected = [
            'name'    => Factory::LOGGER_COLLECTION,
            'loggers' => [
                $gelfConfig
            ]
        ];

        $this->assertEquals($expected, $config->test);
    }

    public function testAlias()
    {
        $streamConfig = [
            'name'  => Factory::LOGGER_STREAM,
            'level' => LogLevel::CRITICAL,
            'path'  => '(filename)'
        ];

        $configArray = [
            'test'  => 'test1',
            'test1' => 'test2',
            'test2' => $streamConfig
        ];

        $config = new Config($configArray);

        $this->assertEquals($streamConfig, $config->test);
    }
}
