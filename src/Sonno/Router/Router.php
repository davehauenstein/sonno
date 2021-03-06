<?php

/**
 * @category  Sonno
 * @package   Sonno\Router
 * @author    Dave Hauenstein <davehauenstein@gmail.com>
 * @author    Tharsan Bhuvanendran <me@tharsan.com>
 * @author    360i <sonno@360i.com>
 * @copyright Copyright (c) 2011 360i LLC (http://360i.com)
 * @license   http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Router;

use Sonno\Http\Request\RequestInterface,
    Sonno\Http\Exception\NotFoundException,
    Sonno\Http\Exception\MethodNotAllowedException,
    Sonno\Http\Exception\NotAcceptableException,
    Sonno\Configuration\Configuration,
    InvalidArgumentException;

/**
 * Responsible for determing which route will satisfy an incoming HTTP request,
 * the Router examines the routes in a Sonno\Configuration\Configuration object
 * and delivers a subset of those routes by comparing to request path, request
 * method and request content type.
 *
 * @category Sonno
 * @package  Sonno\Router
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */
class Router
{
    /**
     * Resource configuration data.
     *
     * @var \Sonno\Configuration\Configuration
     */
    protected $_config;

    protected $_errorFilters;

    /**
     * Construct a new Application.
     *
     * @param \Sonno\Configuration\Configuration $config Resource
     *        configuration data
     */
    public function __construct($config)
    {
        $this->_config = $config;
    }

    /**
     * Setter for configuration object.
     *
     * @param  \Sonno\Configuration\Configuration $config
     * @return \Sonno\Configuration\Driver\AnnotationDriver Implements fluent
     *         interface.
     */
    public function setConfig(Configuration $config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Getter for configuration object.
     *
     * @return \Sonno\Configuration\Configuration
     */
    public function getConfig()
    {
        if (null === $this->_config) {
            $this->setConfig(new Configuration());
        }

        return $this->_config;
    }

    /**
     * Find candidate routes for an incoming request.
     *
     * @param RequestInterface $request        The incoming request
     * @param array            $pathParameters The set of Path parameters
     *                                         matched on the incoming Request
     *                                         path.
     *
     * @throws \Sonno\Http\Exception\MethodNotAllowedException
     * @throws \Sonno\Http\Exception\NotFoundException
     * @throws \InvalidArgumentException
     * @throws \Sonno\Http\Exception\NotAcceptableException
     *
     * @return array A collection of candidate Route objects.
     *
     * @todo   When filtering candidate routes by matching the incoming
     *         media type, Sonno is ignoring any Content-Type parameters
     *         including the charset. This should be resolved, otherwise there
     *         will be unintended consequences while dealing with charsets and
     *         other Content-Type parameters.
     */
    public function match(RequestInterface $request, &$pathParameters = array())
    {
        if (null == $request) {
            throw new InvalidArgumentException(
                'Missing function argument: request'
            );
        }

        $candidateRoutes = array();
        $allRoutes = $this->_config->getRoutes();

        $requestPath = $request->getRequestUri();
        $requestMethod = $request->getMethod();
        $requestContentType = $request->getContentType();

        // drop the base path from the beginning of the incoming path
        $basePath = $this->_config->getBasePath();
        if ($basePath && strstr($requestPath, $basePath) !== FALSE) {
            $requestPath = substr($requestPath, strlen($basePath));
        }

        // locate matching routes using the incoming request path
        /** @var $route \Sonno\Configuration\Route */
        foreach ($allRoutes as $route) {
            $params = $this->_matchPath($requestPath, $route->getPath());
            if (false !== $params) {
                $pathParameters = $params;
                $candidateRoutes[] = $route;
            }
        }

        if (empty($candidateRoutes)) {
            throw new NotFoundException();
        }

        // filter candidate routes further by matching the incoming request
        // method
        $allowedMethods = array();
        foreach ($candidateRoutes as $i => $route) {
            if ($route->getHttpMethod() != $requestMethod) {
                $allowedMethods[] = $route->getHttpMethod();
                unset($candidateRoutes[$i]);
            }
        }

        if (empty($candidateRoutes)) {
            throw new MethodNotAllowedException($allowedMethods);
        }

        // filter candidate routes further by matching the incoming media type
        if (!empty($requestContentType)) {
            foreach ($candidateRoutes as $i => $route) {
                if (($offset = strpos($requestContentType, ';')) !== false) {
                    $requestContentType = substr(
                        $requestContentType,
                        0,
                        $offset
                    );
                }
                if (!in_array($requestContentType, $route->getConsumes())) {
                    unset($candidateRoutes[$i]);
                }
            }
        }

        if (empty($candidateRoutes)) {
            throw new NotAcceptableException;
        }

        return $candidateRoutes;
    }

    /**
     * Determine if two URI paths match by comparing each path segment in turn
     * and populating a map of variables from the template path to values in
     * the concrete path.
     * Template variables are plain variable names (such as 'id') along with an
     * optional Regular Expression constraint (preceded by a colon [:]).
     * If a RegEx-constrained template variable's corresponding concrete path
     * value does not match the relevant Regular Expression, the paths will fail
     * to match.
     *
     * @param string $concrete The concrete path (no variables)
     * @param string $template The template path (optional variables)
     *
     * @return array|boolean false if the paths don't match.
     */
    protected function _matchPath($concrete, $template)
    {
        $concreteSegments = explode('/', trim($concrete, '/'));
        $templateSegments = explode('/', trim($template, '/'));

        // segment counts must match
        if (count($concreteSegments) != count($templateSegments)) {
            return false;
        }

        $pathParamNames  = array();
        $pathParamValues = array();
        foreach ($templateSegments as $i => $templateSegment) {
            $concreteSegment = $concreteSegments[$i];

            if ($concreteSegment == $templateSegment) {
                continue;
            }

            // rewrite the template segment into a regular expression
            $reSegment = preg_replace_callback(
                '/({[^}]+})/',
                function($matches) use (&$pathParamNames) {
                    // extract only the inside of the curly braces
                    $varName = substr($matches[0], 1, strlen($matches[0])-2);

                    // find the parameter regex constraint, if supplied
                    @list($varName, $reConstraint) = explode(':', $varName);

                    // remember the parameter name, in order of discovery
                    $pathParamNames[] = $varName;

                    // replace the {parameter} token with a regular expression
                    if ($reConstraint) {
                        return '(' . trim($reConstraint) . ')';
                    } else {
                        return '([^\)]+)';
                    }
                },
                $templateSegment
            );

            // match the constructed regular expression against the concrete
            // segment being tested to populate parameter values
            if (preg_match_all("/^$reSegment$/", $concreteSegment, $matches)) {
                foreach (array_splice($matches, 1) as $match) {
                    $pathParamValues[] = $match[0];
                }

                continue;
            }

            // this segment failed to match either directly or via regexp match
            return false;
        }

        // ensure that a parameter value was found for each parameter name
        // discovered
        if (count($pathParamNames) == count($pathParamValues)) {
            if (count($pathParamNames) == 0) {
                return true;
            }

            // map parameter names to values
            return array_combine($pathParamNames, $pathParamValues);
        }

        return false;
    }
}

