<?php

declare(strict_types=1);

namespace Phlib\Logger;

/**
 * @package Phlib\Logger
 */
interface ConfigInterface
{
    public function getLoggerConfig(string $name): array;
}
