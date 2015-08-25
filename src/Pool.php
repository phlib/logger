<?php

namespace Phlib\Logger;

use Psr\Log\LoggerInterface;

/**
 * Class Pool
 * @package Phlib\Logger
 */
class Pool
{

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var LoggerInterface[]
     */
    protected $loggerInstances = [];

    /**
     * @var Factory
     */
    protected $loggerFactory;

    /**
     * @param array $config
     * @param Factory $loggerFactory
     */
    public function __construct(array $config, Factory $loggerFactory)
    {
        $this->config        = $config;
        $this->loggerFactory = $loggerFactory;
    }

    /**
     * @param $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }
    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $name
     * @return LoggerInterface
     */
    public function getLogger($name)
    {
        if (!isset($this->loggerInstances[$name])) {
            $this->loggerInstances[$name] = $this->createLogger($name);
        }
        return $this->loggerInstances[$name];
    }

    /**
     * @param string $name
     * @return Collection
     */
    public function getLoggerCollection($name)
    {
        $logger = $this->getLogger($name);

        if (!$logger instanceof Collection) {
            $logger = $this->loggerFactory->createCollectionLogger($name, [
                'loggers' => [$logger]
            ]);
        }

        return $logger;
    }

    /**
     * @param string $name
     * @return LoggerInterface
     */
    protected function createLogger($name)
    {
        $loggerConfig = $name;
        do {
            $loggerConfig = isset($this->config[$loggerConfig]) ? $this->config[$loggerConfig] : [];
        } while (is_string($loggerConfig));

        if (!is_array($loggerConfig)) {
            $loggerConfig = [];
        }

        if (!isset($loggerConfig['name'])) {
            $loggerConfig = [
                'name'    => Factory::LOGGER_COLLECTION,
                'loggers' => $loggerConfig
            ];
        }

        return $this->loggerFactory->createLogger($this->prefix . $name, $loggerConfig);
    }

    /**
     * @param string $name
     * @return LoggerInterface
     */
    public function __get($name)
    {
        return $this->getLogger($name);
    }

}
