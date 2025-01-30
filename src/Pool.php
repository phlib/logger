<?php

declare(strict_types=1);

namespace Phlib\Logger;

use Phlib\Logger\LoggerType\Collection;
use Psr\Log\LoggerInterface;

/**
 * @package Phlib\Logger
 */
class Pool
{
    protected string $prefix = '';

    /**
     * @var LoggerInterface[]
     */
    protected array $loggerInstances = [];

    public function __construct(
        protected ConfigInterface $config,
        protected Factory $loggerFactory,
    ) {
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
