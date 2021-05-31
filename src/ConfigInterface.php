<?php

declare(strict_types=1);

namespace Phlib\Logger;

/**
 * Interface ConfigInterface
 * @package Phlib\Logger
 */
interface ConfigInterface
{

    /**
     * @param string $name
     * @return array
     */
    public function getLoggerConfig($name);
}
