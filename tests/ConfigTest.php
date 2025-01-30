<?php

declare(strict_types=1);

namespace Phlib\Logger\Test;

use Phlib\Logger\Config;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class ConfigTest extends TestCase
{
    public function testEmptyConfig(): void
    {
        $configArray = [];
        $config = new Config($configArray);

        $expected = [
            'type' => 'collection',
            'loggers' => [],
        ];

        static::assertEquals($expected, $config->getLoggerConfig('test'));
    }

    public function testStream(): void
    {
        $streamConfig = [
            'type' => 'stream',
            'path' => '(filename)',
        ];

        $configArray = [
            'test' => $streamConfig,
        ];

        $config = new Config($configArray);

        static::assertEquals($streamConfig, $config->getLoggerConfig('test'));
    }

    public function testCollectionCoerce(): void
    {
        $gelfConfig = [
            'type' => 'gelf',
        ];

        $configArray = [
            'test' => [
                $gelfConfig,
            ],
        ];

        $config = new Config($configArray);

        $expected = [
            'type' => 'collection',
            'loggers' => [
                $gelfConfig,
            ],
        ];

        static::assertEquals($expected, $config->getLoggerConfig('test'));
    }

    public function testAlias(): void
    {
        $streamConfig = [
            'type' => 'stream',
            'level' => LogLevel::CRITICAL,
            'path' => '(filename)',
        ];

        $configArray = [
            'test' => 'test1',
            'test1' => 'test2',
            'test2' => $streamConfig,
        ];

        $config = new Config($configArray);

        static::assertEquals($streamConfig, $config->getLoggerConfig('test'));
    }

    public function testInvalidLoggerConfigType(): void
    {
        $configArray = [
            'test' => false,
        ];

        $config = new Config($configArray);

        $expected = [
            'type' => 'collection',
            'loggers' => [],
        ];

        static::assertEquals($expected, $config->getLoggerConfig('test'));
    }
}
