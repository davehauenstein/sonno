<?php

/**
 * @category  Sonno
 * @package   Sonno\Configuration
 * @author    Dave Hauenstein <davehauenstein@gmail.com>
 * @author    Tharsan Bhuvanendran <me@tharsan.com>
 * @author    360i <sonno@360i.com>
 * @copyright Copyright (c) 2011 360i LLC (http://360i.com)
 * @license   http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Configuration;

/**
 * An immutable class to represent a route. A route is unique and defined by
 * four pieces of data: Path, Http Method, Produces, and Consumes.
 *
 * @category Sonno
 * @package  Sonno\Configuration
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 */
class Route
{
    /**
     * Class to instantiate for a particular route/http method.
     *
     * @var string
     */
    protected $_resourceClassName;

    /**
     * Key value pairs of instance-variable => context-type. Available context
     * types are:
     * - Request: An instance of Sonno\Http\Request\Request representing the
     *   current http request.
     *
     * @var array
     */
    protected $_contexts = array();

    /**
     * Method to call for a particular route/http method.
     *
     * @var string
     */
    protected $_resourceMethodName;

    /**
     * Path to match a class#method to (segment extracted from the class level).
     *
     * @var string
     */
    protected $_classPath;

    /**
     * Path to match a class#method to (segment extracted from the method level).
     *
     * @var string
     */
    protected $_methodPath;

    /**
     * @see \Sonno\Annotation\HttpMethod
     * @see \Sonno\Annotation\DELETE
     * @see \Sonno\Annotation\GET
     * @see \Sonno\Annotation\HEAD
     * @see \Sonno\Annotation\OPTIONS
     * @see \Sonno\Annotation\POST
     * @see \Sonno\Annotation\PUT
     * @var string
     */
    protected $_httpMethod;

    /**
     * @see \Sonno\Annotation\Consumes
     * @var string
     */
    protected $_consumes = array();

    /**
     * @see \Sonno\Annotation\Produces
     * @var string
     */
    protected $_produces = array();

    /**
     * @see \Sonno\Annotation\CookieParam
     * @var array<string>
     */
    protected $_cookieParams = array();

    /**
     * @see \Sonno\Annotation\FormParam
     * @var array<string>
     */
    protected $_formParams = array();

    /**
     * @see \Sonno\Annotation\HeaderParam
     * @var array<string>
     */
    protected $_headerParams = array();

    /**
     * @see \Sonno\Annotation\PathParam
     * @var array<string>
     */
    protected $_pathParams = array();

    /**
     * @see \Sonno\Annotation\QueryParam
     * @var array<string>
     */
    protected $_queryParams = array();

    /**
     * Construct a new Route configuration object.
     *
     * @param $params array
     */
    public function __construct($params)
    {
        $this->_setParams($params);
    }

    /**
     * Getter for _classPath property.
     *
     * @return string _classPath property.
     */
    public function getClassPath()
    {
        return $this->_classPpath;
    }

    /**
     * Getter for _methodPath property.
     *
     * @return string _methodPath property.
     */
    public function getMethodPath()
    {
        return $this->_methodPath;
    }

    /**
     * Getter for the complete Path value.
     *
     * @return string The complete path.
     */
    public function getPath()
    {
        return $this->_classPath . $this->_methodPath;
    }

    /**
     * Getter for _httpMethod property.
     *
     * @return string _httpMethod property.
     */
    public function getHttpMethod()
    {
        return $this->_httpMethod;
    }

    /**
     * Getter for _consumes property.
     *
     * @return string _consumes property.
     */
    public function getConsumes()
    {
        return $this->_consumes;
    }

    /**
     * Getter for _produces property.
     *
     * @return string _produces property.
     */
    public function getProduces()
    {
        return $this->_produces;
    }

    /**
     * Getter for _contexts property.
     *
     * @return array _contexts property.
     */
    public function getContexts()
    {
        return $this->_contexts;
    }

    /**
     * Getter for _cookieParams property.
     *
     * @return array _cookieParams property.
     */
    public function getCookieParams()
    {
        return $this->_cookieParams;
    }

    /**
     * Getter for _formParams property.
     *
     * @return array _formParams property.
     */
    public function getFormParams()
    {
        return $this->_formParams;
    }

    /**
     * Getter for _headerParams property.
     *
     * @return array _headerParams property.
     */
    public function getHeaderParams()
    {
        return $this->_headerParams;
    }

    /**
     * Getter for _pathParams property.
     *
     * @return array _pathParams property.
     */
    public function getPathParams()
    {
        return $this->_pathParams;
    }

    /**
     * Getter for _queryParams property.
     *
     * @return array _queryParams property.
     */
    public function getQueryParams()
    {
        return $this->_queryParams;
    }

    /**
     * Getter for _resourceClassName property.
     *
     * @return string _resourceClassName property.
     */
    public function getResourceClassName()
    {
        return $this->_resourceClassName;
    }

    /**
     * Getter for _resourceMethodName property.
     *
     * @return string _resourceMethodName property.
     */
    public function getResourceMethodName()
    {
        return $this->_resourceMethodName;
    }

    /**
     * Setter for _classPath property. Will trim trailing forward slash '/'
     * and ensure a forward slash '/' exists at the beginning of the string.
     *
     * @param $path string Value for $_path property.
     * @return \Sonno\Configuration\Route Implements fluent interface.
     */
    protected function _setClassPath($path)
    {
        $this->_classPath = '/' . trim($path, '/');
        return $this;
    }

    /**
     * Setter for _methodPath property. Will trim trailing forward slash '/'
     * and ensure a forward slash '/' exists at the beginning of the string.
     *
     * @param $path string Value for $_path property.
     * @return \Sonno\Configuration\Route Implements fluent interface.
     */
    protected function _setMethodPath($path)
    {
        $this->_methodPath = '/' . trim($path, '/');
        return $this;
    }

    /**
     * Setter for _httpMethod property. Will ensure that http method is
     * uppercase.
     *
     * @param $httpMethod string Value for $_httpMethod property.
     * @return \Sonno\Configuration\Route Implements fluent interface.
     */
    protected function _setHttpMethod($httpMethod)
    {
        $this->_httpMethod = strtoupper($httpMethod);
        return $this;
    }

    /**
     * Setter for _contexts property. Will ensure that contexts are always
     * an array.
     *
     * @param $contexts array Value for $_contexts property.
     * @return \Sonno\Configuration\Route Implements fluent interface.
     */
    protected function _setContexts(array $contexts)
    {
        $this->_contexts = $contexts;
        return $this;
    }

    /**
     * Setter for _cookieParams property.
     *
     * @param $cookieParams array Value for $_cookieParams property.
     * @return \Sonno\Configuration\Route Implements fluent interface.
     */
    protected function _setCookieParams(array $cookieParams)
    {
        $this->_cookieParams = $cookieParams;
        return $this;
    }

    /**
     * Setter for _formParams property.
     *
     * @param $formParams array Value for $_formParams property.
     * @return \Sonno\Configuration\Route Implements fluent interface.
     */
    protected function _setFormParams(array $formParams)
    {
        $this->_pathParams = $pathParams;
        return $this;
    }

    /**
     * Setter for _headerParams property.
     *
     * @param $headerParams array Value for $_headerParams property.
     * @return \Sonno\Configuration\Route Implements fluent interface.
     */
    protected function _setHeaderParams(array $headerParams)
    {
        $this->_headerParams = $headerParams;
        return $this;
    }

    /**
     * Setter for _pathParams property.
     *
     * @param $pathParams array Value for $_pathParams property.
     * @return \Sonno\Configuration\Route Implements fluent interface.
     */
    protected function _setPathParams(array $pathParams)
    {
        $this->_pathParams = $pathParams;
        return $this;
    }

    /**
     * Setter for _queryParams property.
     *
     * @param $queryParams array Value for $_queryParams property.
     * @return \Sonno\Configuration\Route Implements fluent interface.
     */
    protected function _setQueryParams(array $queryParams)
    {
        $this->_queryParams = $queryParams;
        return $this;
    }

    /**
     * Setter for all properties. Supported params are:
     *
     * - classPath
     * - methodPath
     * - contexts
     * - method
     * - httpMethod
     * - consumes
     * - produces
     * - cookieParams
     * - formParams
     * - headerParams
     * - pathParams
     * - queryParams
     *
     * @param  $params array Key/Value pairs for all other params.
     * @return \Sonno\Configuration\Route Implements fluent interface.
     */
    protected function _setParams($params)
    {

        foreach ($params as $key => $val) {
            $prop  = '_' . $key;
            if (property_exists($this, $prop)) {
                $method = '_set' . ucfirst($key);
                if (method_exists($this, $method)) {
                    $this->{$method}($val);
                } else {
                    $this->{$prop} = $val;
                }
            }
        }
        return $this;
    }
}
