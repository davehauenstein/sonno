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
    Sonno\Http\Request\RequestInterface,
    Sonno\Http\Request\Request;

/**
 * URI template aware utility class for building URIs from their components.
 *
 * @category Sonno
 * @package  Sonno\Http\Uri
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

    public function __construct(Configuration $config, RequestInterface $request)
    {
        $this->_uriComponents = array();
        $this->_config  = $config;

        $requestHeaders = $request->getHeaders();
        $this->scheme($request->isSecure() ? 'https' : 'http');
        if (isset($requestHeaders['Host'])) {
            $this->host($requestHeaders['Host']);
        }
        $this->port($request->getPort());
        $this->_queryParams = $request->getQueryParams();

        $requestUri = $request->getRequestUri();
        if (($hashOffset = strpos('#', $requestUri)) !== FALSE) {
            $this->path(strstr($requestUri, '#', true));
            $this->fragment(substr(strstr($requestUri, '#'), 1));
        } else {
            $this->path($requestUri);
        }
    }

    /**
     * Set the URI scheme.
     *
     * @param string $scheme the URI scheme, may contain URI template parameters.
     *                       A null value will unset the URI scheme. 
     * @return Sonno\Http\Uri\UriBuilder
     */
    public function scheme($scheme)
    {
        $this->_uriComponents['scheme'] = $scheme;
        return $this;
    }

    /**
     * Set the URI host.
     *
     * @param string $host the URI host, may contain URI template parameters.
     *                     A null value will unset the host component of the URI. 
     * @return Sonno\Http\Uri\UriBuilder
     */
    public function host($host)
    {
        $this->_uriComponents['host'] = $host;
        return $this;
    }

    /**
     * Set the URI port.
     *
     * @param int $port the URI port, a value of -1 will unset an explicit port.
     * @return Sonno\Http\Uri\UriBuilder
     */
    public function port($port)
    {
        $this->_uriComponents['port'] = $port;
        return $this;
    }

    /**
     * Append path to the existing path.
     *
     * @param string $path the path, may contain URI template parameters
     * @return Sonno\Http\Uri\UriBuilder
     * @todo Implement this
     */
    public function path($path)
    {
        return $this;
    }

    /**
     * Append the path from a Path-annotated class and/or method to the
     * existing path.
     *
     * @param string $resourceClassName The FQNS and class name of the
     *                                  Path-annotated class
     * @param string $resourceMethodName The name of the Path-annotated method
     * @return Sonno\Http\Uri\UriBuilder
     * @todo Implement this
     */
    public function resourcePath(
        $resourceClassName,
        $resourceMethodName = null
    ) {
        return $this;
    }

    /**
     * Set the URI path. This method will overwrite any existing path and
     * associated matrix parameters. Existing '/' characters are preserved thus
     * a single value can represent multiple URI path segments.
     *
     * @param string $path the path, may contain URI template parameters.
     *                     A null value will unset the path component of the URI.
     * @return Sonno\Http\Uri\UriBuilder
     * @todo Implement this
     */
    public function replacePath($path)
    {
        return $this;
    }

    /**
     * Append a query parameter to the existing set of query parameters.
     *
     * @param string $key the query parameter name, may contain URI template
     *                    parameters
     * @param string $value the query parameter value, may contain URI template
     *                      parameters
     * @return Sonno\Http\Uri\UriBuilder
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
     * @param string $querystring the URI query string, may contain URI template
     *                            parameters. A null value will remove all query
     *                            parameters.
     * @return Sonno\Http\Uri\UriBuilder
     */
    public function replaceQuery($querystring)
    {
        return $this;
    }

    /**
     * Set the URI fragment.
     *
     * @param string $fragment the URI fragment, may contain URI template
     *                         parameters. A null value will remove any existing
     *                         fragment. 
     * @return Sonno\Http\Uri\UriBuilder
     */
    public function fragment($fragment)
    {
        $this->_uriComponents['port'] = $fragment;
        return $this;
    }

    /**
     * Build a URI, using the supplied values in order to replace any URI
     * template parameters.
     *
     * @param array $values An ordered array of URI template parameter values.
     * @return string
     */
    public function build(array $values = array())
    {
    }

    /**
     * Build a URI, any URI template parameters will be replaced by the value
     * in the supplied map.
     *
     * @param array $values An associative array of URI template parameter values.
     * @return string
     */
    public function buildFromMap(array $values = array())
    {
    }
}

