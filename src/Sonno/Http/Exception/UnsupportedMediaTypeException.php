<?php

/**
 * @category   Sonno
 * @package    Sonno\Http\Exception
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 * @author     Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Http\Exception;

use \Exception;

/**
 * An exception that occurs when a resource class has been located, but can't
 * produce a representation in the media type desired.
 *
 * @category   Sonno
 * @package    Sonno\Http\Exception
 * @author     Tharsan Bhuvanendran <me@tharsan.com>
 */
class UnsupportedMediaTypeException extends Exception
{
}
