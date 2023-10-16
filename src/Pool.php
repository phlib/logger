<?php

declare(strict_types=1);

namespace Phlib\Logger;

use Phlib\Logger\LoggerType\Collection;
use Psr\Log\LoggerInterface;

/**
 * Class Pool
 * @package Phlib\Logger
 */
class Pool
{
    /**
     * @var ConfigInterface
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

    public function __construct(ConfigInterface $config, Factory $loggerFactory)
    {
        $this->config = $config;
        $this->loggerFactory = $loggerFactory;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getLogger(string $name): LoggerInterface
    {
        if (!isset($this->loggerInstances[$name])) {
            $this->loggerInstances[$name] = $this->createLogger($name);
        }
        return $this->loggerInstances[$name];
    }

    public function getLoggerCollection(string $name): Collection
    {
        $logger = $this->getLogger($name);

        if (!$logger instanceof Collection) {
            $logger = $this->loggerFactory->createCollectionLogger($name, [
                'loggers' => [$logger],
            ]);
        }

        return $logger;
    }

    protected function createLogger(string $name): LoggerInterface
    {
        $loggerConfig = $this->config->getLoggerConfig($name);

        return $this->loggerFactory->createLogger($this->getPrefix() . $name, $loggerConfig);
    }

    public function __get(string $name): LoggerInterface
    {
        return $this->getLogger($name);
    }
}
