<?php

/**
 * @category  Sonno
 * @package   Sonno\Dispatcher
 * @author    Dave Hauenstein <davehauenstein@gmail.com>
 * @author    Tharsan Bhuvanendran <me@tharsan.com>
 * @author    360i <sonno@360i.com>
 * @copyright Copyright (c) 2011 360i LLC (http://360i.com)
 * @license   http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Dispatcher;

use Sonno\Application\WebApplicationException,
    Sonno\Http\Request\RequestInterface,
    Sonno\Configuration\Route,
    Sonno\Uri\UriInfo;

/**
 * Responsible for executing code specified by a Route.
 *
 * @category Sonno
 * @package  Sonno\Dispatcher
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */
class Dispatcher
{
    /**
     * The incoming HTTP request.
     *
     * @var Sonno\Request\RequestInterface
     */
    protected $_request;

    /**
     * Information about the URI.
     *
     * @var Sonno\Uri\UriInfo
     */
    protected $_uriInfo;

    /**
     * Construct a new Application.
     *
     * @param Sonno\Request\RequestInterface $request The incoming HTTP request.
     * @param Sonno\Uri\UriInfo $uriInfo Information about the URI.
     */
    public function __construct(RequestInterface $request, UriInfo $uriInfo)
    {
        $this->_request = $request;
        $this->_uriInfo = $uriInfo;
    }

    /**
     * Setter for request object.
     *
     * @param  Sonno\Request\RequestInterface $request
     * @return Sonno\Dispatcher\Dispatcher Implements fluent interface.
     */
    public function setConfig(Configuration $config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Getter for request object.
     *
     * @return Sonno\Request\RequestInterface
     */
    public function getConfig()
    {
        return $this->_request;
    }

    /**
     * Dispatch the current HTTP request to the specified route.
     * Instantiate the resource class specified by the route, and then execute
     * the resource class method specified by the route using data coalesced
     * from certain sources.
     *
     * @param Sonno\Configuration\Route $route The selected route to execute.
     * @return mixed
     */
    public function dispatch(Route $route)
    {
        // obtain Reflection objects for the resource method selected
        $reflClass  = new \ReflectionClass($route->getResourceClassName());
        $reflMethod = $reflClass->getMethod($route->getResourceMethodName());

        // instantiate the selected resource class
        $resource = $this->_createResourceInstance(
            $route->getResourceClassName()
        );

        // construct a flat array of method arguments for the resource method
        $pathParamValues     = $this->_uriInfo->getPathParameters();
        $queryParamValues    = $this->_uriInfo->getQueryParameters();
        $headerParamValues   = $this->_request->getHeaders();
        $pathParams          = $route->getPathParams() ?: array();
        $queryParams         = $route->getQueryParams() ?: array();
        $headerParams        = $route->getHeaderParams() ?: array();
        $formParams          = $route->getFormParams() ?: array();
        $resourceMethodArgs  = array();

        parse_str($this->_request->getRequestBody(), $formParamValues);

        foreach ($reflMethod->getParameters() as $idx => $reflParam) {
            $parameterName = $reflParam->getName();

            // search for an argument value in the Path parameter collection
            if (in_array($parameterName, $pathParams)
                && isset($pathParamValues[$parameterName])
            ) {
                $resourceMethodArgs[$idx] = $pathParamValues[$parameterName];
            }

            // search for an argument value in the Query parameter collection
            if (in_array($parameterName, $queryParams)
                && isset($queryParamValues[$parameterName])
            ) {
                $resourceMethodArgs[$idx] = $queryParamValues[$parameterName];
            }

            // search for an argument value in the Header parameter collection
            if (in_array($parameterName, $headerParams)
                && isset($headerParamValues[$parameterName])
            ) {
                $resourceMethodArgs[$idx] = $headerParamValues[$parameterName];
            }

            // search for an argument value in the Form parameter collection
            if (in_array($parameterName, $formParams)
                && isset($formParamValues[$parameterName])
            ) {
                $resourceMethodArgs[$idx] = $formParamValues[$parameterName];
            }
        }

        // inject Context variables into the resource class instance
        $contextInjections = array(
            'Request' => $this->_request,
            'UriInfo' => $this->_uriInfo
        );

        foreach ($route->getContexts() as $propertyName => $contextType) {
            try {
                $reflProperty = $reflClass->getProperty($propertyName);
            } catch(\ReflectionException $e) {
                continue;
            }

            $reflProperty->setAccessible(true);

            if (isset($contextInjections[$contextType])) {
                $reflProperty->setValue(
                    $resource,
                    $contextInjections[$contextType]
                );
            }
        }

        // execute the selected resource method using the generated method
        // arguments
        try {
            return $reflMethod->invokeArgs($resource, $resourceMethodArgs);
        } catch(WebApplicationException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Instantiate a named class, and return the instance.
     *
     * @param $className string The name of the class to instantiate.
     * @return object
     */
    protected function _createResourceInstance($className)
    {
        return new $className;
    }
}

