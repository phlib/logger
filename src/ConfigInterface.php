<?php

declare(strict_types=1);

namespace Phlib\Logger;

/**
 * Interface ConfigInterface
 * @package Phlib\Logger
 */
interface ConfigInterface
{

    public function getLoggerConfig(string $name): array;
}
