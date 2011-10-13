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
    Sonno\Http\Exception\UnsupportedMediaTypeException;

/**
 * An immutable class to represent a route and http method combination for a
 * particular class#method combination.
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
     * @return \Sonno\Configuration\AnnotationDriver Implements fluent
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
     * @param Sonno\Http\Request\RequestInterface $request The incoming request
     * @param array $pathParameters The set of Path parameters matched on the
     *                              incoming Request path.
     * @return array A collection of candidate Route objects.
     * @throws \Sonno\Http\Exception\InvalidArgumentException
     * @throws \Sonno\Http\Exception\NotFoundException
     * @throws \Sonno\Http\Exception\MethodNotAllowedException
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
        foreach ($allRoutes as $route) {
            if ($this->matchPath($requestPath, $route->getPath(), $params)) {
                $pathParameters = $params;
                $candidateRoutes[] = $route;
            }
        }

        if (empty($candidateRoutes)) {
            throw new NotFoundException();
        }

        // filter candidate routes further by matching the incoming request
        // method
        foreach ($candidateRoutes as $i => $route) {
            if ($route->getHttpMethod() != $requestMethod) {
                unset($candidateRoutes[$i]);
            }
        }

        if (empty($candidateRoutes)) {
            throw new MethodNotAllowedException;
        }

        // filter candidate routes further by matching the incoming media type
        if (!empty($requestContentType)) {
            foreach ($candidateRoutes as $i => $route) {
                if (!in_array($requestContentType, $route->getConsumes())) {
                    unset($candidateRoutes[$i]);
                }
            }
        }

        if (empty($candidateRoutes)) {
            throw new UnsupportedMediaTypeException;
        }

        return $candidateRoutes;
    }

    /**
     * Determine if two URI paths match by comparing each path segment in turn
     * and populating a map of variables in the template path to values from the
     * concrete path.
     *
     * @param string $concrete The concrete path (no variables)
     * @param string $template The template path (optional variables)
     * @return boolean True if the paths match.
     */
    private function matchPath($concrete, $template, &$pathParams)
    {
        $concreteSegments = explode('/', trim($concrete, '/'));
        $templateSegments = explode('/', trim($template, '/'));

        // segment counts must match
        if (count($concreteSegments) != count($templateSegments)) {
            return false;
        }

        $pathParams = array();
        foreach ($templateSegments as $i => $templateSegment) {
            $concreteSegment = $concreteSegments[$i];

            if ($concreteSegment == $templateSegment) {
                continue;
            } else if (preg_match('/{(.+)}/', $templateSegment, $tmplMatches)) {
                // template segment is a variable
                $varName = $tmplMatches[1];

                if (strstr($varName, ':')) {
                    // template varible value must match a regular expression
                    list($varName, $regexpConstraint) = explode(':', $varName);
                    $regexpConstraint = trim($regexpConstraint);
                    if (!preg_match("/$regexpConstraint/", $concreteSegment)) {
                        return false;
                    } else {
                        $pathParams[$varName] = $concreteSegment;
                    }
                } else {
                    $pathParams[$varName] = $concreteSegment;
                }
            } else {
                return false;
            }
        }

        return true;
    }
}

