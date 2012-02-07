<?php

/**
 * @category   Sonno
 * @package    Sonno\Logger\Adapter
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 * @author     Tharsan Bhuvanendran <me@tharsan.com>
 * @author     360i <sonno@360i.com>
 * @copyright  Copyright (c) 2011 360i LLC (http://360i.com)
 * @license    http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Logger\Adapter;

use Sonno\Logger\LoggerInterface,
    Monolog\Logger;

/**
 * A Monolog logging adapter. All logging requests are proxied to the Monlog
 * logger that is set via the constructor.
 *
 * This adapter uses {@link https://github.com/Seldaek/monolog "Monolog"}'s
 * Logger class. Monolog is a PHP 5.3 logger, written by Jordi Boggiano.
 *
 * @category   Sonno
 * @package    Sonno\Logger\Adapter
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 */
class MonologAdapter implements LoggerInterface
{
    /**
     * An instance of a Monolog logger.
     *
     * @var Monolog\Logger
     */
    protected $_logger;

    /**
     * Construct a new instance of a Monolog Logger Adapter.
     *
     * @param Monolog\Logger $logger An instance of a Monolog logger.
     */
    public function __construct(Logger $logger)
    {
        return $this->_logger = $logger;
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * @param string $message The log message
     * @param array $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function debug($message, array $context = array())
    {
        return $this->_logger->debug($message, $context);
    }

    /**
     * Adds a log record at the INFO level.
     *
     * @param string $message The log message
     * @param array $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function info($message, array $context = array())
    {
        return $this->_logger->info($message, $context);
    }

    /**
     * Adds a log record at the INFO level.
     *
     * @param string $message The log message
     * @param array $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function notice($message, array $context = array())
    {
        return $this->_logger->notice($message, $context);
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * @param string $message The log message
     * @param array $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function warn($message, array $context = array())
    {
        return $this->_logger->warn($message, $context);
    }

    /**
     * Adds a log record at the ERROR level.
     *
     * @param string $message The log message
     * @param array $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function err($message, array $context = array())
    {
        return $this->_logger->err($message, $context);
    }

    /**
     * Adds a log record at the CRITICAL level.
     *
     * @param string $message The log message
     * @param array $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function crit($message, array $context = array())
    {
        return $this->_logger->crit($message, $context);
    }

    /**
     * Adds a log record at the ALERT level.
     *
     * @param string $message The log message
     * @param array $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function alert($message, array $context = array())
    {
        return $this->_logger->alert($message, $context);
    }

    /**
     * Adds a log record at the EMERG level.
     *
     * @param string $message The log message
     * @param array $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function emerg($message, array $context = array())
    {
        return $this->_logger->emerg($message, $context);
    }
}
