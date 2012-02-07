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

use Sonno\Logger\LoggerInterface;

/**
 * An adapter that throws log records away.
 *
 * @category   Sonno
 * @package    Sonno\Logger\Adapter
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 */
class NullAdapter implements LoggerInterface
{
    /**
     * Throws away a log record at the DEBUG level.
     *
     * @param string $message The log message
     * @param array $context The log context
     * @return Boolean Always returns true.
     */
    public function debug($message, array $context = array())
    {
        return true;
    }

    /**
     * Throws away a log record at the INFO level.
     *
     * @param string $message The log message
     * @param array $context The log context
     * @return Boolean Always returns true.
     */
    public function info($message, array $context = array())
    {
        return true;
    }

    /**
     * Throws away a log record at the INFO level.
     *
     * @param string $message The log message
     * @param array $context The log context
     * @return Boolean Always returns true.
     */
    public function notice($message, array $context = array())
    {
        return true;
    }

    /**
     * Throws away a log record at the WARNING level.
     *
     * @param string $message The log message
     * @param array $context The log context
     * @return Boolean Always returns true.
     */
    public function warn($message, array $context = array())
    {
        return true;
    }

    /**
     * Throws away a log record at the ERROR level.
     *
     * @param string $message The log message
     * @param array $context The log context
     * @return Boolean Always returns true.
     */
    public function err($message, array $context = array())
    {
        return true;
    }

    /**
     * Throws away a log record at the CRITICAL level.
     *
     * @param string $message The log message
     * @param array $context The log context
     * @return Boolean Always returns true.
     */
    public function crit($message, array $context = array())
    {
        return true;
    }

    /**
     * Throws away a log record at the ALERT level.
     *
     * @param string $message The log message
     * @param array $context The log context
     * @return Boolean Always returns true.
     */
    public function alert($message, array $context = array())
    {
        return true;
    }

    /**
     * Throws away a log record at the EMERG level.
     *
     * @param string $message The log message
     * @param array $context The log context
     * @return Boolean Always returns true.
     */
    public function emerg($message, array $context = array())
    {
        return true;
    }
}
