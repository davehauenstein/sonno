<?php

/**
 * @category   Sonno
 * @package    Sonno\Http\Exception
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 * @author     Tharsan Bhuvanendran <me@tharsan.com>
 * @author     360i <sonno@360i.com>
 * @copyright  Copyright (c) 2011 360i LLC (http://360i.com)
 * @license    http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Http\Exception;

use Sonno\Application\WebApplicationException;

/**
 * An exception that occurs when a resource class has been located, but cannot
 * produce a suitable representation.
 *
 * @category   Sonno
 * @package    Sonno\Http\Exception
 * @author     Tharsan Bhuvanendran <me@tharsan.com>
 */
class NotAcceptableException extends WebApplicationException
{
    public function __construct()
    {
        parent::__construct(406);
    }
}
