<?php

namespace Phlib\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class LoggerFactory
 * @package Phlib\Logger
 */
class LoggerFactory
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var LoggerInterface[]
     */
    protected $loggerInstances = [];

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
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
        if (isset($this->loggerInstances[$name])) {
            return $this->loggerInstances[$name];
        }

        $config = $name;
        do {
            $config = isset($this->config[$config])?$this->config[$config]:[];

        } while (is_string($config));

        if (!is_array($config)) {
            $config = [];
        }

        $logger = $this->createLogger($this->prefix . $name, $config);

        $this->loggerInstances[$name] = $logger;

        return $logger;
    }

    /**
     * @param string $name
     * @param array $config
     * @return Collection
     */
    public function createLogger($name, array $config)
    {
        $loggerCollection = new Collection();

        foreach ($config as $index => $loggerConfig) {
            if (!isset($loggerConfig['name'])) {
                throw new \RuntimeException(
                    sprintf(
                        'Logger config missing name at index %d',
                        $index
                    )
                );
            }

            $methodName = "{$loggerConfig['name']}Logger";
            if (!method_exists($this, $methodName)) {
                throw new \RuntimeException(
                    sprintf(
                        'Cannot find a logger named "%s"',
                        $loggerConfig['name']
                    )
                );
            }

            $logger = $this->$methodName($name, $loggerConfig);

            $logLevel = isset($loggerConfig['level'])?$loggerConfig['level']:LogLevel::DEBUG;

            if ($logLevel !== LogLevel::DEBUG) {
                $logger = new LevelFilter(
                    $logger,
                    $logLevel
                );
            }

            $loggerCollection->add($logger);
        }

        return $loggerCollection;
    }

    /**
     * @param string $name
     * @param array $config
     * @return \Gelf\Logger
     */
    protected function gelfLogger($name, array $config)
    {

        $host = isset($config['host'])?$config['host']:false;
        $port = isset($config['port'])?$config['port']:12201;

        $transport = new \Gelf\Transport\UdpTransport($host, $port);
        $messagePublisher = new \Gelf\Publisher($transport);

        return new \Gelf\Logger($messagePublisher, $name);
    }

    /**
     * @param string $name
     * @param array $config
     * @return Stream
     */
    protected function streamLogger($name, array $config)
    {
        return new Stream($name, isset($config['path'])?$config['path']:false);
    }

    /**
     * @param $name
     * @return Collection
     */
    public function __get($name)
    {
        return $this->getLogger($name);
    }
}
