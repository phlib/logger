<?php

declare(strict_types=1);

namespace Phlib\Logger\LoggerType;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * Write to CLI with colour!
 *
 * @package Phlib\Logger
 */
class CliColor extends Stream
{
    private readonly OutputFormatter $formatter;

    /**
     * @see Stream::__construct()
     * @param resource|string $stream Optional. Default to Standard Error
     */
    public function __construct(string $name, $stream = STDERR)
    {
        parent::__construct($name, $stream);

        $this->formatter = new OutputFormatter(true, [
            'debug' => new OutputFormatterStyle(),
            'info' => new OutputFormatterStyle('blue'),
            'notice' => new OutputFormatterStyle('green'),
            'warning' => new OutputFormatterStyle('yellow'),
            'error' => new OutputFormatterStyle('red'),
            'critical' => new OutputFormatterStyle('red', 'yellow'),
            'alert' => new OutputFormatterStyle('white', 'red', ['bold']),
            'emergency' => new OutputFormatterStyle('white', 'red', ['bold', 'underscore']),
        ]);
    }

    protected function getMessageFormat(mixed $level, array $context = []): string
    {
        $parentFormat = parent::getMessageFormat($level, $context);
        $consoleFormat = "<{$level}>{$parentFormat}</{$level}>";

        return $this->formatter->format($consoleFormat);
    }
}
