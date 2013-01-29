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

use Sonno\Http\Response\Response;

/**
 * Runtime exception for applications.
 *
 * @category   Sonno
 * @package    Sonno\Application
 * @author     Tharsan Bhuvanendran <me@tharsan.com>
 */
class WebApplicationException extends \Exception
{
    /**
     * The HTTP response for the unexpected web application error.
     *
     * @var \Sonno\Http\Response\Response
     */
    protected $_response;

    /**
     * Create a new web application Exception.
     *
     * @param int   $status  HTTP status code to respond to the request with.
     * @param mixed $message Response entity to respond to the request with.
     */
    public function __construct($status, $message = null)
    {
        if ($status instanceof Response) {
            $this->_response = $status;
        } else if (is_integer($status)) {
            $this->_response = new Response($status, $message);
        }
    }

    /**
     * Retrieve the HTTP response for the unexpected web application error.
     *
     * @return \Sonno\Http\Response\Response
     */
    public function getResponse()
    {
        return $this->_response;
    }
}
