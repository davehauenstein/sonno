<?php

/**
 * @category  Sonno
 * @package   Sonno\Uri
 * @author    Dave Hauenstein <davehauenstein@gmail.com>
 * @author    Tharsan Bhuvanendran <me@tharsan.com>
 * @author    360i <sonno@360i.com>
 * @copyright Copyright (c) 2011 360i LLC (http://360i.com)
 * @license   http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Uri;

use Sonno\Configuration\Configuration,
    Sonno\Http\Request\RequestInterface,
    Sonno\Http\Request\Request;

/**
 * URI template aware utility class for building URIs from their components.
 *
 * @category Sonno
 * @package  Sonno\Uri
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */
class UriBuilder
{
    /**
     * @var array
     */
    protected $_uriComponents;

    /**
     * @var array
     */
    protected $_queryParams;

    /**
     * @var Sonno\Configuration\Configuration
     */
    protected $_config;

    public function __construct(
        Configuration $config,
        RequestInterface $request)
    {
        $this->_uriComponents = array();
        $this->_config  = $config;

        $requestHeaders = $request->getHeaders();

        $this->scheme($request->isSecure() ? 'https' : 'http');
        if (isset($requestHeaders['host'])) {
            $this->host($requestHeaders['host']);
        }

        // set the port number using the one from the Request, or the default
        // for the request scheme
        if ($request->getPort()) {
            $this->port($request->getPort());
        } else if ('http' == $this->_uriComponents['scheme']) {
            $this->port(80);
        } else if ('ftp' == $this->_uriComponents['scheme']) {
            $this->port(21);
        }

        $this->_queryParams = $request->getQueryParams();

        $requestUri = $request->getRequestUri();
        if (($hashOffset = strpos('#', $requestUri)) !== FALSE) {
            $this->replacePath(strstr($requestUri, '#', true));
            $this->fragment(substr(strstr($requestUri, '#'), 1));
        } else {
            $this->replacePath($requestUri);
        }
    }

    /**
     * Set the URI scheme.
     *
     * @param string $scheme the URI scheme, may contain URI template
     *                       parameters.
     *                       A null value will unset the URI scheme
     * @return Sonno\Uri\UriBuilder
     */
    public function scheme($scheme)
    {
        if (null == $scheme) {
            unset($this->_uriComponents['scheme']);
            return $this;
        }

        $this->_uriComponents['scheme'] = $scheme;
        return $this;
    }

    /**
     * Set the URI host.
     *
     * @param string $host the URI host, may contain URI template parameters.
     *                     A null value will unset the host component of the URI
     * @return Sonno\Uri\UriBuilder
     */
    public function host($host)
    {
        if (null == $host) {
            unset($this->_uriComponents['host']);
            return $this;
        }

        $this->_uriComponents['host'] = $host;
        return $this;
    }

    /**
     * Set the URI port.
     *
     * @param int $port the URI port, a value of -1 will unset an explicit port
     * @return Sonno\Uri\UriBuilder
     */
    public function port($port)
    {
        if (-1 == $port) {
            unset($this->_uriComponents['port']);
            return $this;
        }

        $this->_uriComponents['port'] = $port;
        return $this;
    }

    /**
     * Append path to the existing path.
     *
     * @param string $path the path, may contain URI template parameters
     * @return Sonno\Uri\UriBuilder
     */
    public function path($path)
    {
        if (empty($path)) {
            return $this;
        }

        $currentPath = $this->_uriComponents['path'];

        // reset the current path when it represents the root
        if ($currentPath == '/') {
            $currentPath = '';
        }

        $currentPath .= '/' . trim($path, '/');

        $this->_uriComponents['path'] = $currentPath;

        return $this;
    }

    /**
     * Append the path from a Path-annotated class and/or method to the
     * existing path.
     *
     * @param string $resourceClassName The FQNS and class name of the
     *                                  Path-annotated class
     * @param string $resourceMethodName The name of the Path-annotated method
     * @return Sonno\Uri\UriBuilder
     * @todo Implement this
     */
    public function resourcePath(
        $resourceClassName,
        $resourceMethodName = null)
    {
        $foundClass = false;

        foreach ($this->_config->getRoutes() as $route) {
            if ($resourceClassName == $route->getResourceClassName()) {
                if (!$foundClass) {
                    $foundClass = true;
                    $this->path($route->getClassPath());
                }

                if ($resourceMethodName == $route->getResourceMethodName()) {
                    $this->path($route->getMethodPath());
                    return $this;
                }

            }
        }

        return $this;
    }

    /**
     * Set the URI path. This method will overwrite any existing path.
     * Existing '/' characters are preserved thus a single value can represent
     * multiple URI path segments.
     *
     * @param string $path the path, may contain URI template parameters.
     *                     A null value will unset the path component of the URI
     * @return Sonno\Uri\UriBuilder
     */
    public function replacePath($path)
    {
        if (null == $path) {
            unset($this->_uriComponents['path']);
            return $this;
        }

        $this->_uriComponents['path'] = '/' . trim($path, '/');
        return $this;
    }

    /**
     * Append a query parameter to the existing set of query parameters.
     *
     * @param string $key the query parameter name, may contain URI template
     *                    parameters
     * @param string $value the query parameter value, may contain URI template
     *                      parameters
     * @return Sonno\Uri\UriBuilder
     */
    public function queryParam($key, $value)
    {
        $this->_queryParams[$key] = $value;
        return $this;
    }

    /**
     * Set the URI query string. This method will overwrite any existing query
     * parameters.
     *
     * @param string $query the URI query string, may contain URI template
     *                      parameters. A null value will remove all query
     *                      parameters
     * @return Sonno\Uri\UriBuilder
     */
    public function replaceQuery($query)
    {
        if (null == $query) {
            $this->_queryParams = array();
            return $this;
        }

        parse_str($query, $this->_queryParams);
        return $this;
    }

    /**
     * Set the URI fragment.
     *
     * @param string $fragment the URI fragment, may contain URI template
     *                         parameters. A null value will remove any existing
     *                         fragment. 
     * @return Sonno\Uri\UriBuilder
     */
    public function fragment($fragment)
    {
        if (null == $fragment) {
            unset($this->_uriComponents['fragment']);
            return $this;
        }

        $this->_uriComponents['fragment'] = $fragment;
        return $this;
    }

    /**
     * Build a URI, using the supplied values in order to replace any URI
     * template parameters.
     *
     * @param array $values An ordered array of URI template parameter values.
     * @return string
     * @throws LengthException if there are any URI template parameters without
     *                         a supplied value
     */
    public function build(array $values = array())
    {
        $uri = $this->_concatUriComponents();

        // perform URI template value substitution
        $countMatches = preg_match_all('/{([^}]*)}/', $uri, $matches);
        if ($countMatches > count($values)) {
            throw new \LengthException(
                sprintf(
                    'Need %d URI template values, but only %d values supplied.',
                    $countMatches,
                    count($values)
                )
            );
        } else if ($countMatches == count($values)) {
            foreach ($matches[0] as $idx => $varName) {
                $uri = str_replace($varName, $values[$idx], $uri);
            }
        }

        return $uri;
    }

    /**
     * Build a URI, any URI template parameters will be replaced by the value
     * in the supplied map.
     *
     * @param array $values An associative array of URI template parameter
     *                      values.
     * @return string
     */
    public function buildFromMap(array $values = array())
    {
        $uri = $this->_concatUriComponents();

        foreach ($values as $varName => $varValue) {
            $uri = str_replace("{{$varName}}", $varValue, $uri);
        }

        return $uri;
    }

    /**
     * Concatenate all stored URI components into a single string URI.
     *
     * @return string
     */
    protected function _concatUriComponents()
    {
        if (isset($this->_uriComponents['scheme'])) {
            $uri = $this->_uriComponents['scheme'] . '://';
        }

        if (isset($this->_uriComponents['host'])) {
            $uri .= $this->_uriComponents['host'];
        }

        // append the port number when required
        if (('http' == $this->_uriComponents['scheme'] &&
                80 != $this->_uriComponents['port']) ||
            ('ftp' == $this->_uriComponents['scheme'] &&
                21 != $this->_uriComponents['port'])
        ) {
            $uri .= ':' . $this->_uriComponents['port'];
        }

        if (isset($this->_uriComponents['path'])) {
            $uri .= $this->_uriComponents['path'];
        }

        if (count($this->_queryParams)) {
            $uri .= '?' . http_build_query($this->_queryParams);
        }

        if (isset($this->_uriComponents['fragment'])) {
            $uri .= '#' . $this->_uriComponents['fragment'];
        }

        return $uri;
    }
}

