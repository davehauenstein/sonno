<?php

/**
 * @category  Sonno
 * @package   Sonno\Http\Uri
 * @author    Dave Hauenstein <davehauenstein@gmail.com>
 * @author    Tharsan Bhuvanendran <me@tharsan.com>
 * @author    360i <sonno@360i.com>
 * @copyright Copyright (c) 2011 360i LLC (http://360i.com)
 * @license   http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Http\Uri;

use Sonno\Configuration\Configuration,
    Sonno\Configuration\Route,
    Sonno\Http\Request\RequestInterface;

/**
 * An injectable interface that provides access to application and request URI
 * information.
 *
 * @category Sonno
 * @package  Sonno\Http\Uri
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 *
 * @todo JAX-RS implementation: getAbsolutePath()
 * @todo JAX-RS implementation: getAbsolutePathBuilder()
 */
class UriInfo
{
    /**
     * Application configuration.
     *
     * @var Sonno\Configuration\Configuration
     */
    protected $_config;

    /**
     * The incoming request.
     *
     * @var Sonno\Http\Request\RequestInterface
     */
    protected $_request;

    /**
     * The matched route.
     *
     * @var Sonno\Configuration\Route
     */
    protected $_route;

    /**
     * The request path parameter values.
     *
     * @var array
     */
    protected $_pathParams = array();

    /**
     * The request querystring parameter values.
     *
     * @var array
     */
    protected $_queryParams = array();

    public function __construct(
        Configuration $config,
        RequestInterface $request,
        Route $route)
    {
        $this->_setConfiguration($config);
        $this->_setRequest($request);
        $this->_route = $route;
    }

    /**
     * Getter for application configuration.
     */
    public function getConfiguration()
    {
        return $this->_config;
    }

    /**
     * Getter for the incoming request.
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Get the absolute path of the request. This includes everything preceding
     * the path (host, port etc) but excludes query parameters.
     *
     * @todo Build an absolute path (includes everything preceding the path)
     * @return string
     */
    public function getAbsolutePath()
    {
    }

    /**
     * Get the absolute path of the request in the form of a UriBuilder. This
     * includes everything preceding the path (host, port etc) but excludes
     * query parameters. 
     *
     * @todo
     * @return Sonno\Http\Uri\UriBuilder
     */
    public function getAbsolutePathBuilder()
    {
        return null;
    }

    /**
     * Get the base URI of the application. URIs of root resource classes are
     * all relative to this base URI.
     *
     * @return string
     */
    public function getBaseUri()
    {
        return $this->_config->getBaseUri();
    }

    /**
     * Get the base URI of the application in the form of a UriBuilder.
     *
     * @todo
     * @return Sonno\Http\Uri\UriBuilder
     */
    public function getBaseUriBuilder()
    {
        return null;
    }

    /**
     * Get the path of the current request relative to the base URI as a string.
     *
     * @todo
     * @return string
     */
    public function getPath()
    {
    }

    /**
     * Get the values of any embedded URI template parameters.
     *
     * @return array
     */
    public function getPathParameters()
    {
        return $this->_pathParams;
    }

    /**
     * Get the URI query parameters of the current request. The map keys are the
     * names of the query parameters.
     *
     * @return array
     */
    public function getQueryParameters()
    {
        return $this->_queryParams;
    }

    /**
     * Get the absolute request URI including any query parameters.
     *
     * @return string
     */
    public function getRequestUri()
    {
        return $this->_request->getRequestUri();
    }

    /**
     * Get the absolute request URI in the form of a UriBuilder.
     *
     * @todo
     * @return Sonno\Http\Uri\UriBuilder
     */
    public function getRequestUriBuilder()
    {
        return null;
    }

    /**
     * Get the values of all embedded URI template parameters.
     *
     * @param array $pathParameters The new values for URI template parameters.
     * @return void
     */
    public function setPathParameters($pathParameters)
    {
        $this->_pathParams = $pathParameters;
    }

    /**
     * Set the URI query parameters of the current request.
     *
     * @param $pathParameters The new values for URI query parameters.
     * @return void
     */
    public function setQueryParameters($queryParameters)
    {
        $this->_queryParams = $queryParameters;
    }

    /**
     * Set the application configuration.
     *
     * @param Sonno\Configuration\Configuration $config The application
     *                                                  configuration data.
     * @return void
     */
    protected function _setConfiguration(Configuration $config)
    {
        $this->_config = $config;
    }

    /**
     * Set the incoming request.
     *
     * @param Sonno\Http\Request\RequestInterface $request The incoming request.
     * @return void
     */
    protected function _setRequest(RequestInterface $request)
    {
        $this->_request = $request;
    }

}
