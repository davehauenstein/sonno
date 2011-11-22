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
    }

    /**
     * Test setting a URI host.
     *
     * @return void
     */
    public function testHost()
    {
    }

    /**
     * Test setting a URI port.
     *
     * @return void
     */
    public function testPort()
    {
    }

    /**
     * Test setting a URI path.
     *
     * @return void
     */
    public function testPath()
    {
    }

    /**
     * Test replacing the whole path.
     *
     * @return void
     */
    public function testReplacePath()
    {
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
    protected function buildMockRequest($uri, $headers)
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

        if ($headers) {
            $request
                ->expects($this->any())
                ->method('getHeaders')
                ->will($this->returnValue($headers));
        }

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

