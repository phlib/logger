<?php

declare(strict_types=1);

namespace Phlib\Logger\LoggerType;

use Psr\Log\AbstractLogger;

/**
 * @package Phlib\Logger
 */
class Stream extends AbstractLogger
{
    private readonly string $name;

    /**
     * @var resource
     */
    private $stream;

    private string $messageFormat = '[{datetime}] {name}.{level}: {message} {context}';

    private string $dateFormat = 'Y-m-d H:i:s';

    /**
     * @param resource|string $stream
     */
    public function __construct(string $name, $stream)
    {
        $this->name = trim(str_replace(["\r", "\n"], '', $name));

        if (!is_resource($stream)) {
            $stream = @fopen($stream, 'a');
            if ($stream === false) {
                throw new \RuntimeException('Unable to open stream for given path');
            }
        }
        $this->stream = $stream;
    }

    public function setMessageFormat(string $format): self
    {
        $this->messageFormat = $format;

        return $this;
    }

    /**
     * Get current message format
     *
     * This method can be overridden by extending classes to modify the behaviour
     */
    protected function getMessageFormat(mixed $level, array $context = []): string
    {
        return $this->messageFormat;
    }

    public function setDateFormat(string $format): self
    {
        $this->dateFormat = $format;

        return $this;
    }

    public function log(mixed $level, string|\Stringable $message, array $context = []): void
    {
        $meta = [
            'datetime' => date($this->dateFormat),
            'name' => $this->name,
            'level' => $level,
            'message' => $this->formatMessage((string)$message, $context),
            'context' => $this->formatContext($context),
        ];

        $message = static::interpolate($this->getMessageFormat($level, $context), $meta);

        fwrite($this->stream, $message . PHP_EOL);
    }

    protected function formatMessage(string $message, array $context): string
    {
        $message = static::interpolate($message, $context);

        return trim(str_replace(["\r", "\n"], ' ', $message));
    }

    protected function formatContext(array $context): string
    {
        if (isset($context['exception']) and $context['exception'] instanceof \Exception) {
            $context['exception'] = (string)$context['exception'];
        }

        return json_encode($context, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * Reference implementation
     * @link https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md#12-message
     *
     * Expanded on reference implementation to include handling for complex types,
     * trying to ensure no error, warning or notice is happening
     */
    private static function interpolate(string $message, array $context): string
    {
        // build a replacement array with braces around the context keys
        $replace = [];

        foreach ($context as $key => $val) {
            $val = static::contextValueToString($val);
            $replace['{' . $key . '}'] = $val;
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /**
     * Converts a context value into its appropriate string representation
     *
     * @param mixed $val
     */
    private static function contextValueToString($val): string
    {
        if (is_bool($val)) {
            return var_export($val, true);
        } elseif (is_scalar($val)) {
            return (string)$val;
        } elseif ($val === null) {
            return 'NULL';
        } elseif (is_object($val)) {
            if (is_callable([$val, '__toString'])) {
                return (string)$val;
            }
            return $val::class;
        }
        return gettype($val);
    }
}
