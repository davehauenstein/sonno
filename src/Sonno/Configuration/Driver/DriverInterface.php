<?php

/**
 * @category   Sonno
 * @package    Sonno\Configuration\Driver
 * @subpackage Configuration
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 * @author     Tharsan Bhuvanendran <me@tharsan.com>
 * @author     360i <sonno@360i.com>
 * @copyright  Copyright (c) 2011 360i LLC (http://360i.com)
 * @license    http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Configuration\Driver;

/**
 * Contract for config drivers.
 *
 * @category   Sonno
 * @package    Sonno\Configuration\Driver
 * @subpackage Configuration
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 */
interface DriverInterface
{
    /**
     * A method that must be implemented by all config drivers that is used
     * to generate a Configuration object by parsing some type of configuration
     * (YAML, XML, Annotations, etc..). It must return an instance of
     * \Sonno\Configuration\Configuration.
     *
     * @return \Sonno\Configuration\Configuration
     */
    public function parseConfig();
}
