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
 * An exception that occurs when a resource class has been located, but doesn't
 * support the requested HTTP method.
 *
 * @category   Sonno
 * @package    Sonno\Http\Exception
 * @author     Tharsan Bhuvanendran <me@tharsan.com>
 */
class MethodNotAllowedException extends WebApplicationException
{
    /**
     * @param array $allowedMethods HTTP methods that are allowed.
     */
    public function __construct(array $allowedMethods = array())
    {
        parent::__construct(405);

        $this->_response->setHeaders(
            array('Allow' => implode(', ', $allowedMethods))
        );
    }
}
