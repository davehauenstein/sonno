<?php

/**
 * @category Sonno
 * @package  Sonno\Test\Http\Uri
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Http\Uri;

require_once __DIR__ . '/Asset/TestResource.php';

use Sonno\Configuration\Configuration,
    Sonno\Http\Request\RequestInterface,
    Sonno\Http\Request\Request,
    Sonno\Http\Uri\UriBuilder;

/**
 * Test suite for the Sonno\Test\Http\Uri\UriBuilder class.
 *
 * @category Sonno
 * @package  Sonno\Test\Http\Uri
 */
class UriBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test setting a URI scheme.
     *
     * @return void
     */
    public function testScheme()
    {
        $request = $this->buildMockRequest(
            '/test/example',
            array('Host' => 'example.com')
        );
        $config  = $this->buildMockConfiguration();

        // setup an additional expectation on the Request for a port number
        $request
            ->expects($this->any())
            ->method('getPort')
            ->will($this->returnValue(21));

        $builder = new UriBuilder($config, $request);
        $uri     = $builder->scheme('ftp')->build();

        $this->assertEquals('ftp://example.com/test/example', $uri);
    }

    /**
     * Test setting a URI host.
     *
     * @return void
     */
    public function testHost()
    {
        $request = $this->buildMockRequest(
            '/test/example',
            array('Host' => 'example.com')
        );
        $config  = $this->buildMockConfiguration();

        $builder = new UriBuilder($config, $request);
        $uri     = $builder->host('www.asciisauce.net')->build();

        $this->assertEquals('http://www.asciisauce.net/test/example', $uri);
    }

    /**
     * Test setting a URI port.
     *
     * @return void
     */
    public function testPort()
    {
        $request = $this->buildMockRequest(
            '/test/example',
            array('Host' => 'example.com')
        );
        $config  = $this->buildMockConfiguration();

        $builder = new UriBuilder($config, $request);
        $uri     = $builder->port(31415)->build();

        $this->assertEquals('http://example.com:31415/test/example', $uri);
    }

    /**
     * Test appending a single path segment to the URI path.
     *
     * @return void
     */
    public function testAppendingSingleSegmentToPath()
    {
        $request = $this->buildMockRequest(
            '/test/example',
            array('Host' => 'example.com')
        );
        $config  = $this->buildMockConfiguration();

        $builder = new UriBuilder($config, $request);
        $uri     = $builder->path('monkey')->build();

        $this->assertEquals('http://example.com/test/example/monkey', $uri);
    }

    /**
     * Test appending multiple path segments to the URI path.
     *
     * @return void
     */
    public function testAppendingMultipleSegmentsToPath()
    {
        $request = $this->buildMockRequest(
            '/test/example',
            array('Host' => 'example.com')
        );
        $config  = $this->buildMockConfiguration();

        $builder = new UriBuilder($config, $request);
        $uri     = $builder->path('/donkey/kong/')->build();

        $this->assertEquals('http://example.com/test/example/donkey/kong', $uri);
    }

    /**
     * Test replacing the whole path.
     *
     * @return void
     */
    public function testReplacePath()
    {
        $request = $this->buildMockRequest(
            '/test/example',
            array('Host' => 'example.com')
        );
        $config  = $this->buildMockConfiguration();

        $builder = new UriBuilder($config, $request);
        $uri     = $builder->replacePath('/donkey/kong/')->build();

        $this->assertEquals('http://example.com/donkey/kong', $uri);
    }

    /**
     * Test replacing the whole path with one derived from a class annotation.
     *
     * @return void
     */
    public function testPathFromResourceClassAnnotation()
    {
    }

    /**
     * Test replacing the whole path with one derived from a class method
     * annotation.
     *
     * @return void
     */
    public function testPathFromResourceMethodAnnotation()
    {
    }

    /**
     * Test setting a query parameter.
     *
     * @return void
     */
    public function testQueryParam()
    {
    }

    /**
     * Test replacing the entire query string.
     *
     * @return void
     */
    public function testReplaceQuery()
    {
    }

    /**
     * Test setting the URI fragment.
     *
     * @return void
     */
    public function testFragment()
    {
    }

    /**
     * Generate a mock Sonno\Http\Request\RequestInterface object.
     *
     * @return Sonno\Http\Request\RequestInterface
     */
    protected function buildMockRequest($uri, $headers = array())
    {
        $request = $this
            ->getMockBuilder('Sonno\Http\Request\Request')
            ->disableOriginalConstructor()
            ->getMock();

        if ($uri) {
            $request
                ->expects($this->any())
                ->method('getRequestUri')
                ->will($this->returnValue($uri));
        }

        $request
            ->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue($headers));

        return $request;
    }

    /**
     * Generate a mock Sonno\Configuration\Configuration object.
     *
     * @return Sonno\Configuration\Configuration
     */
    protected function buildMockConfiguration(
        $routeOptions = array(),
        $baseUri = null
    ) {
        $config = $this
            ->getMockBuilder('Sonno\Configuration\Configuration')
            ->disableOriginalConstructor()
            ->getMock();

        $routes = array();
        foreach ($routeOptions as $routeOption) {
            $route = $this
                ->getMockBuilder('Sonno\Configuration\Route')
                ->disableOriginalConstructor()
                ->getMock();

            // setup method expectations on this Route for each route option
            foreach ($routeOption as $key => $value) {
                $methodName = 'get' . ucfirst($key);
                $route
                    ->expects($this->any())
                    ->method($methodName)
                    ->will($this->returnValue($value));
            }

            $routes[] = $route;
        }

        $config
            ->expects($this->any())
            ->method('getRoutes')
            ->will($this->returnValue($routes));

        if ($baseUri) {
            $config
                ->expects($this->any())
                ->method('getBasePath')
                ->will($this->returnValue($baseUri));
        }

        return $config;
    }
}

