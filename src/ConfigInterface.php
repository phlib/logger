<?php

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
