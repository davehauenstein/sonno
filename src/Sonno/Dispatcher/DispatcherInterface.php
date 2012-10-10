<?php

/**
 * @category  Sonno
 * @package   Sonno\Dispatcher
 * @author    Dave Hauenstein <davehauenstein@gmail.com>
 * @author    Tharsan Bhuvanendran <me@tharsan.com>
 * @author    360i <sonno@360i.com>
 * @copyright Copyright (c) 2012 360i LLC (http://360i.com)
 * @license   http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Dispatcher;

use Sonno\Configuration\Route,
    Sonno\Http\Request\RequestInterface,
    Sonno\Uri\UriInfo;

/**
 * Responsible for executing code specified by a Route.
 *
 * @category Sonno
 * @package  Sonno\Dispatcher
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

interface DispatcherInterface
{
    /**
     * Dispatch the current HTTP request to the specified route.
     * Instantiate the resource class specified by the route, and then execute
     * the resource class method specified by the route using data coalesced
     * from certain sources.
     *
     * @param \Sonno\Configuration\Route $route The selected route to execute.
     * @return mixed
     */
    public function dispatch(Route $route);
}