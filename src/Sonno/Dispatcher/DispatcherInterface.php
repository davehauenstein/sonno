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

use Sonno\Configuration\Route;

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

    /**
     * Set the incoming HTTP request.
     *
     * @param \Sonno\Http\Request\RequestInterface $request
     * @return \Sonno\Dispatcher\DispatcherInterface Implements fluent
     *                                               interface.
     */
    public function setRequest(\Sonno\Http\Request\RequestInterface $request);

    /**
     * Set the URI info.
     *
     * @param \Sonno\Uri\UriInfo $uriInfo
     * @return \Sonno\Dispatcher\DispatcherInterface Implements fluent
     *                                               interface.
     */
    public function setUriInfo(\Sonno\Uri\UriInfo $uriInfo);
}