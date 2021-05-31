<?php

declare(strict_types=1);

namespace Phlib\Logger;

use Psr\Log\LoggerInterface;

/**
 * Class Factory
 * @package Phlib\Logger
 */
class Factory
{
    /**
     * @var array
     */
    private $decorators = [
        'defaultContext' => \Phlib\Logger\Decorator\DefaultContext::class,
        'level' => \Phlib\Logger\Decorator\LevelFilter::class
    ];

    /**
     * Register a new decorator class
     *
     * Class must implement DecoratorInterface
     *
     * @param string $configKey
     * @param string $className
     */
    public function registerDecorator(string $configKey, string $className): void
    {
        if (isset($this->decorators[$configKey])) {
            throw new \RuntimeException('Decorator key already in use: ' . $configKey);
        }
        if (in_array($className, $this->decorators)) {
            throw new \RuntimeException('Decorator class already registered: ' . $className);
        }
        $this->decorators[$configKey] = $className;
    }

    public function unregisterDecorator(string $configKey): void
    {
        if (!isset($this->decorators[$configKey])) {
            throw new \RuntimeException('Decorator key not registered: ' . $configKey);
        }
        unset($this->decorators[$configKey]);
    }

    public function createLogger(string $name, array $config): LoggerInterface
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

    private function applyDecorators(LoggerInterface $logger, array $config): LoggerInterface
    {
        foreach ($this->decorators as $configKey => $decoratorClassName) {
            if (!isset($config[$configKey])) {
                continue;
            }
            if (!class_exists($decoratorClassName)) {
                throw new \RuntimeException('Decorator class not found: ' . $decoratorClassName);
            }
            if (!is_subclass_of($decoratorClassName, \Phlib\Logger\Decorator\AbstractDecorator::class)) {
                throw new \RuntimeException('Decorator class invalid: ' . $decoratorClassName);
            }
            /** @var \Phlib\Logger\Decorator\AbstractDecorator $logger */
            $logger = new $decoratorClassName($logger, $config[$configKey]);
        }

        return $logger;
    }

    public function createCollectionLogger(string $name, array $config): LoggerType\Collection
    {
        $loggerCollection = new LoggerType\Collection();
        foreach ($config['loggers'] as $index => $logger) {
            if (!$logger instanceof LoggerInterface) {
                try {
                    $logger = $this->createLogger($name, $logger);
                } catch (\DomainException $e) {
                    $message = sprintf('%s at index %d', $e->getMessage(), $index);
                    throw new \DomainException($message, 0, $e);
                }
            }
            $loggerCollection->add($logger);
        }
        return $loggerCollection;
    }

    public function createStreamLogger(string $name, array $config): LoggerType\Stream
    {
        $path = $config['path'] ?? false;
        return new LoggerType\Stream($name, $path);
    }

    public function createGelfLogger(string $name, array $config): \Gelf\Logger
    {
        $host = $config['host'] ?? false;
        $port = $config['port'] ?? 12201;

        $transport        = new \Gelf\Transport\UdpTransport($host, $port);
        $messagePublisher = new \Gelf\Publisher($transport);

        return new \Gelf\Logger($messagePublisher, $name);
    }
}
