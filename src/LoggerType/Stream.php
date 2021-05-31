<?php

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
    public function __construct($name, $stream)
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

    /**
     * @param string $format
     * @return $this
     */
    public function setMessageFormat($format)
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
    protected function getMessageFormat($level, array $context = array())
    {
        return $this->messageFormat;
    }

    /**
     * @param string $format
     * @return $this
     */
    public function setDateFormat($format)
    {
        $this->dateFormat = $format;

        return $this;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        $meta = [
            'datetime' => date($this->dateFormat),
            'name'     => $this->name,
            'level'    => $level,
            'message'  => $this->formatMessage($message, $context),
            'context'  => $this->formatContext($context)
        ];

        $message = static::interpolate($this->getMessageFormat($level, $context), $meta);

        fwrite($this->stream, $message . PHP_EOL);
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    protected function formatMessage($message, array $context)
    {
        $message = static::interpolate($message, $context);
        $message = trim(str_replace(["\r", "\n"], ' ', $message));

        return $message;
    }

    /**
     * @param array $context
     * @return string
     */
    protected function formatContext(array $context)
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
    private static function interpolate($message, array $context)
    {
        // build a replacement array with braces around the context keys
        $replace = array();

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
    private static function contextValueToString($val)
    {

        if (is_bool($val)) {
            return var_export($val, true);
        } elseif (is_scalar($val)) {
            return (string)$val;
        } elseif (is_null($val)) {
            return 'NULL';
        } elseif (is_object($val)) {
            if (is_callable(array($val, '__toString'))) {
                return (string)$val;
            }
            return get_class($val);
        }
        return gettype($val);
    }
}
