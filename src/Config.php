<?php

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

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getLoggerConfig($name)
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

    /**
     * @param string $name
     * @return array
     */
    protected function resolveAliases($name)
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
