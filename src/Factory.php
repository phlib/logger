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
    private $decorators = [
        'level' => '\Phlib\Logger\Decorator\LevelFilter'
    ];

    /**
     * Register a new decorator class
     *
     * Class must implement DecoratorInterface
     *
     * @param string $configKey
     * @param string $className
     */
    public function registerDecorator($configKey, $className)
    {
        if (isset($this->decorators[$configKey])) {
            throw new \RuntimeException('Decorator key already in use: ' . $configKey);
        }
        if (in_array($className, $this->decorators)) {
            throw new \RuntimeException('Decorator class already registered: ' . $className);
        }
        $this->decorators[$configKey] = $className;
    }

    /**
     * Un-register a decorator class
     *
     * @param string $configKey
     */
    public function unregisterDecorator($configKey)
    {
        if (!isset($this->decorators[$configKey])) {
            throw new \RuntimeException('Decorator key not registered: ' . $configKey);
        }
        unset($this->decorators[$configKey]);
    }

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
            throw new \DomainException('Logger type cannot be empty');
        }
        $methodName = sprintf('create%sLogger', ucfirst($type));
        if (!method_exists($this, $methodName)) {
            throw new \DomainException(sprintf('Cannot find a logger type named "%s"', $type));
        }
        $logger = $this->$methodName($name, $config);

        $logger = $this->applyDecorators($logger, $config);

        return $logger;
    }

    /**
     * Apply any available decorators to the logger, if configured
     *
     * @param LoggerInterface $logger
     * @param array $config
     *
     * @return LoggerInterface
     */
    private function applyDecorators(LoggerInterface $logger, array $config)
    {
        foreach ($this->decorators as $configKey => $decoratorClassName) {
            if (!isset($config[$configKey])) {
                continue;
            }
            if (!class_exists($decoratorClassName)) {
                throw new \RuntimeException('Decorator class not found: ' . $decoratorClassName);
            }
            if (!is_subclass_of($decoratorClassName, '\Phlib\Logger\Decorator\AbstractDecorator')) {
                throw new \RuntimeException('Decorator class invalid: ' . $decoratorClassName);
            }
            /** @var \Phlib\Logger\Decorator\AbstractDecorator $logger */
            $logger = new $decoratorClassName($logger, $config[$configKey]);
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
