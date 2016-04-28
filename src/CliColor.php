<?php

namespace Phlib\Logger;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * Class CliColor
 *
 * Write to CLI with colour!
 *
 * @package Phlib\Logger
 */
class CliColor extends Stream
{
    /**
     * @var string
     */
    private $originalMessageFormat;

    /**
     * @var OutputFormatter
     */
    protected $formatter;

    /**
     * @see Stream::__construct()
     * @param string $name
     * @param resource|string $stream Optional. Default to Standard Error
     */
    public function __construct($name, $stream = STDERR)
    {
        parent::__construct($name, $stream);

        $this->originalMessageFormat = $this->messageFormat;

        $this->formatter = new OutputFormatter(true, [
            'debug'     => new OutputFormatterStyle(),
            'info'      => new OutputFormatterStyle('blue'),
            'notice'    => new OutputFormatterStyle('green'),
            'warning'   => new OutputFormatterStyle('yellow'),
            'error'     => new OutputFormatterStyle('red'),
            'critical'  => new OutputFormatterStyle('red', 'yellow'),
            'alert'     => new OutputFormatterStyle('white', 'red', ['bold']),
            'emergency' => new OutputFormatterStyle('white', 'red', ['bold', 'underscore'])
        ]);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @see Stream::log()
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        $format = "<{$level}>{$this->originalMessageFormat}</{$level}>";
        $this->messageFormat = $this->formatter->format($format);

        parent::log($level, $message, $context);
    }

    /**
     * @see Stream::setMessageFormat()
     * @param string $format
     * @return $this
     */
    public function setMessageFormat($format)
    {
        $this->originalMessageFormat = $format;

        return parent::setMessageFormat($format);
    }

}
