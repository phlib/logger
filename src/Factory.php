<?php

namespace Phlib\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class Factory
 * @package Phlib\Logger
 */
class Factory
{
    /**
     * @param string $name
     * @param array $config
     * @return LoggerInterface
     * @throws \DomainException
     */
    public function createLogger($name, array $config)
    {
        if (!isset($config['type'])) {
            throw new \DomainException('Logger config missing logger type');
        }
        $type = trim($config['type']);
        if (!$type) {
            throw new \DomainException(sprintf('Logger type cannot be empty', $type));
        }
        $methodName = sprintf('create%sLogger', ucfirst($type));
        if (!method_exists($this, $methodName)) {
            throw new \DomainException(sprintf('Cannot find a logger type named "%s"', $type));
        }
        $logger   = $this->$methodName($name, $config);
        $logLevel = isset($config['level']) ? $config['level'] : LogLevel::DEBUG;
        if ($logLevel !== LogLevel::DEBUG) {
            return new LevelFilter($logger, $logLevel);
        }
        return $logger;
    }

    /**
     * @param string $name
     * @param array $config
     * @return Collection
     * @throws \DomainException
     */
    public function createCollectionLogger($name, $config)
    {
        $loggerCollection = new Collection();
        foreach ($config['loggers'] as $index => $logger) {
            if (!$logger instanceof LoggerInterface) {
                try {
                    $logger = $this->createLogger($name, $logger);
                } catch (\DomainException $e) {
                    $message = sprintf('%s at index %d', $e->getMessage(), $index);
                    throw new \DomainException($message, null, $e);
                }
            }
            $loggerCollection->add($logger);
        }
        return $loggerCollection;
    }

    /**
     * @param string $name
     * @param array $config
     * @return Stream
     */
    public function createStreamLogger($name, $config)
    {
        $path = isset($config['path']) ? $config['path'] : false;
        return new Stream($name, $path);
    }

    /**
     * @param string $name
     * @param array $config
     * @return \Gelf\Logger
     */
    public function createGelfLogger($name, $config)
    {
        $host = isset($config['host']) ? $config['host'] : false;
        $port = isset($config['port']) ? $config['port'] : 12201;

        $transport        = new \Gelf\Transport\UdpTransport($host, $port);
        $messagePublisher = new \Gelf\Publisher($transport);

        return new \Gelf\Logger($messagePublisher, $name);
    }

}
