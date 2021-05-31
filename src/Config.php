<?php

declare(strict_types=1);

namespace Phlib\Logger;

/**
 * Class Config
 * @package Phlib\Logger
 */
class Config implements ConfigInterface
{
    /**
     * @var array
     */
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getLoggerConfig(string $name): array
    {
        $loggerConfig = $this->resolveAliases($name);

        if (!isset($loggerConfig['type'])) {
            $loggerConfig = [
                'type'    => 'collection',
                'loggers' => $loggerConfig
            ];
        }

        return $loggerConfig;
    }

    protected function resolveAliases(string $name): array
    {
        $loggerConfig = $name;

        do {
            $loggerConfig = $this->config[$loggerConfig] ?? [];
        } while (is_string($loggerConfig));

        if (!is_array($loggerConfig)) {
            $loggerConfig = [];
        }

        return $loggerConfig;
    }
}
