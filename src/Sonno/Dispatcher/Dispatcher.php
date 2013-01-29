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
    Sonno\Configuration\Route,
    Sonno\Dispatcher\DispatcherInterface,
    Sonno\Http\Request\RequestInterface,
    Sonno\Http\Variant,
    Sonno\Uri\UriInfo,
    ReflectionClass,
    ReflectionMethod;

/**
 * Responsible for executing code specified by a Route.
 *
 * @category Sonno
 * @package  Sonno\Dispatcher
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */
class Dispatcher implements DispatcherInterface
{
    /**
     * @var \Sonno\Uri\UriInfo
     */
    protected $_uriInfo;

    /**
     * @var \Sonno\Http\Request\RequestInterface
     */
    protected $_request;

    public function __construct(
        RequestInterface $request = null,
        UriInfo $uriInfo = null
    ) {
        $this->_request = $request;
        $this->_uriInfo = $uriInfo;
    }

    /**
     * Set the URI info.
     *
     * @param \Sonno\Uri\UriInfo $uriInfo
     * @return \Sonno\Dispatcher\DispatcherInterface Implements fluent interface.
     */
    public function setUriInfo(\Sonno\Uri\UriInfo $uriInfo)
    {
        $this->_uriInfo = $uriInfo;
        return $this;
    }

    /**
     * Set the incoming HTTP request.
     *
     * @param \Sonno\Http\Request\RequestInterface $request
     * @return \Sonno\Dispatcher\DispatcherInterface Implements fluent interface.
     */
    public function setRequest(\Sonno\Http\Request\RequestInterface $request)
    {
        $this->_request = $request;
        return $this;
    }

    public function dispatch(Route $route) {
        // obtain Reflection objects for the resource method selected
        $reflClass  = new ReflectionClass($route->getResourceClassName());
        $reflMethod = $reflClass->getMethod($route->getResourceMethodName());

        // instantiate the selected resource class
        $resource = $this->_createResourceInstance(
            $route->getResourceClassName()
        );

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
        $methodArgs = $this->_getResourceMethodArguments($route, $reflMethod);

        try {
            return $reflMethod->invokeArgs($resource, $methodArgs);
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

    /**
     * Construct a flat array of method arguments for the resource method.
     *
     * @param Route            $route  The route being processed.
     * @param ReflectionMethod $method The resource class method that arguments
     *                                 are needed for.
     *
     * @return array
     */
    protected function _getResourceMethodArguments(
        Route $route,
        ReflectionMethod $method
    )
    {
        $clsRenderable = 'Sonno\Application\Renderable';

        $pathParamValues     = $this->_uriInfo->getPathParameters();
        $queryParamValues    = $this->_uriInfo->getQueryParameters();
        $headerParamValues   = $this->_request->getHeaders();
        $pathParams          = $route->getPathParams() ?: array();
        $queryParams         = $route->getQueryParams() ?: array();
        $headerParams        = $route->getHeaderParams() ?: array();
        $formParams          = $route->getFormParams() ?: array();
        $resourceMethodArgs  = array();

        parse_str($this->_request->getRequestBody(), $formParamValues);

        foreach ($method->getParameters() as $idx => $reflParam) {
            $parameterName = $reflParam->getName();
            $parameterClass = $reflParam->getClass();

            // if the parameter is a type that implements Renderable, use the
            // implementation's unrender() function to generate an instance
            // of the class from the request body as the parameter value
            if (null !== $parameterClass
                && $parameterClass->implementsInterface($clsRenderable)
            ) {
                $parameterClassName = $parameterClass->getName();
                $parameterValue     = $parameterClassName::unrender(
                    $this->_request->getRequestBody(),
                    new Variant(null, null, $this->_request->getContentType())
                );

                $resourceMethodArgs[$idx] = $parameterValue;
            }

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

        return $resourceMethodArgs;
    }
}

