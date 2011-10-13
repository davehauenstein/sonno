<?php

/**
 * @category   Sonno
 * @package    Sonno\Application
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 * @author     Tharsan Bhuvanendran <me@tharsan.com>
 * @author     360i <sonno@360i.com>
 * @copyright  Copyright (c) 2011 360i LLC (http://360i.com)
 * @license    http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Application;

use \Exception;

/**
 * An exception that occurs when a resource class produces a representation that
 * cannot be understood.
 *
 * @category   Sonno
 * @package    Sonno\Application
 * @author     Tharsan Bhuvanendran <me@tharsan.com>
 */
class MalformedResourceRepresentationException extends Exception
{
}
