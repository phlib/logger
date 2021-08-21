<?php

declare(strict_types=1);

namespace Phlib\Logger\LoggerType;

use Psr\Log\AbstractLogger;

/**
 * Class Stream
 * @package Phlib\Logger
 */
class Stream extends AbstractLogger
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var resource
     */
    private $stream;

    /**
     * @var string
     */
    private $messageFormat = '[{datetime}] {name}.{level}: {message} {context}';

    /**
     * @var string
     */
    private $dateFormat = 'Y-m-d H:i:s';

    /**
     * @param string $name
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
     *
     * @param mixed $level
     * @param array $context
     * @return string
     */
    protected function getMessageFormat($level, array $context = []): string
    {
        return $this->messageFormat;
    }

    public function setDateFormat(string $format): self
    {
        $this->dateFormat = $format;

        return $this;
    }

    /**
     * @param mixed $level
     * @param string|\Stringable $message
     */
    public function log($level, $message, array $context = []): void
    {
        $meta = [
            'datetime' => date($this->dateFormat),
            'name'     => $this->name,
            'level'    => $level,
            'message'  => $this->formatMessage((string)$message, $context),
            'context'  => $this->formatContext($context)
        ];

        $message = static::interpolate($this->getMessageFormat($level, $context), $meta);

        fwrite($this->stream, $message . PHP_EOL);
    }

    protected function formatMessage(string $message, array $context): string
    {
        $message = static::interpolate($message, $context);
        $message = trim(str_replace(["\r", "\n"], ' ', $message));

        return $message;
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
     *
     * @param mixed $message
     * @param array $context
     * @return string
     */
    private static function interpolate($message, array $context): string
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
     * @return string
     */
    private static function contextValueToString($val): string
    {

        if (is_bool($val)) {
            return var_export($val, true);
        } elseif (is_scalar($val)) {
            return (string)$val;
        } elseif (is_null($val)) {
            return 'NULL';
        } elseif (is_object($val)) {
            if (is_callable([$val, '__toString'])) {
                return (string)$val;
            }
            return get_class($val);
        }
        return gettype($val);
    }
}
