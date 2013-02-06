<?php

/**
 * @category Sonno
 * @package  Sonno\Test\Router
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Router;

use Sonno\Router\Router,
    Sonno\Http\Exception\NotFoundException,
    Sonno\Http\Exception\MethodNotAllowedException;

/**
 * Test suite for the Sonno\Test\Router class.
 *
 * @category Sonno
 * @package  Sonno\Test\Router
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that when match() fails to locate a resource class to dispatch to,
     * it throws a NotFoundException.
     *
     * @expectedException \Sonno\Http\Exception\NotFoundException
     */
    public function testThrowsNotFoundWhenNoMatch()
    {
        $config = $this
            ->getMockBuilder('Sonno\Configuration\Configuration')
            ->disableOriginalConstructor()
            ->getMock();
        $config
            ->expects($this->any())
            ->method('getRoutes')
            ->will($this->returnValue(array()));

        $request = $this->buildMockRequest('GET', '/users/abc');

        $router = new Router($config);
        $router->match($request, $params);
    }

    /**
     * Test that when the resource located by match() doesn't support the
     * incoming request method, it throws a MethodNotAllowed exception.
     *
     * @expectedException \Sonno\Http\Exception\MethodNotAllowedException
     */
    public function testThrowsMethodNotAllowedWhenNoMethodMatch()
    {
        $route = $this
            ->getMockBuilder('Sonno\Configuration\Route')
            ->disableOriginalConstructor()
            ->getMock();
        $route
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('/user'));
        $route
            ->expects($this->any())
            ->method('getHttpMethod')
            ->will($this->returnValue('GET'));

        $config = $this
            ->getMockBuilder('Sonno\Configuration\Configuration')
            ->disableOriginalConstructor()
            ->getMock();
        $config
            ->expects($this->any())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $request = $this->buildMockRequest('POST', '/user');

        $router = new Router($config);
        $router->match($request, $params);
    }

    /**
     * Test that when the resource located by match() can't produce a
     * representation of the type desired by the incoming request,
     * it throws a NotAcceptableException exception.
     *
     * @expectedException \Sonno\Http\Exception\NotAcceptableException
     */
    public function testThrowsNotAcceptableExceptionWhenResourceCannotProduceRepresentation()
    {
        $route = $this
            ->getMockBuilder('Sonno\Configuration\Route')
            ->disableOriginalConstructor()
            ->getMock();
        $route
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('/user/nick123'));
        $route
            ->expects($this->any())
            ->method('getHttpMethod')
            ->will($this->returnValue('POST'));
        $route
            ->expects($this->any())
            ->method('getConsumes')
            ->will($this->returnValue(array('application/xml')));

        $config = $this
            ->getMockBuilder('Sonno\Configuration\Configuration')
            ->disableOriginalConstructor()
            ->getMock();
        $config
            ->expects($this->any())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $request = $this->buildMockRequest('POST', '/user/nick123', 'text/plain');

        $router = new Router($config);
        $router->match($request, $params);
    }

    /**
     * Test matching two equal paths.
     *
     * @return void
     */
    public function testPathMatchWithoutParameters()
    {
        $route = $this
            ->getMockBuilder('Sonno\Configuration\Route')
            ->disableOriginalConstructor()
            ->getMock();
        $route
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('/test/foo/bar'));
        $route
            ->expects($this->any())
            ->method('getHttpMethod')
            ->will($this->returnValue('GET'));

        $config = $this
            ->getMockBuilder('Sonno\Configuration\Configuration')
            ->disableOriginalConstructor()
            ->getMock();
        $config
            ->expects($this->any())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $request = $this->buildMockRequest('GET', '/test/foo/bar');

        $router = new Router($config);

        $matches = $router->match($request);

        $this->assertEquals(count($matches), 1);
    }

    /**
     * Test matching two paths that match, with one Path parameter.
     *
     * @return void
     */
    public function testPathMatchWithOneParameters()
    {
        $route = $this
            ->getMockBuilder('Sonno\Configuration\Route')
            ->disableOriginalConstructor()
            ->getMock();
        $route
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('/test/{username}/bar'));
        $route
            ->expects($this->any())
            ->method('getHttpMethod')
            ->will($this->returnValue('GET'));

        $config = $this
            ->getMockBuilder('Sonno\Configuration\Configuration')
            ->disableOriginalConstructor()
            ->getMock();
        $config
            ->expects($this->any())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $request = $this->buildMockRequest('GET', '/test/foo/bar');

        $router = new Router($config);

        $matches = $router->match($request, $params);

        $this->assertEquals(1, count($matches));
        $this->assertEquals(1, count($params));
        $this->assertArrayHasKey('username', $params);
        $this->assertEquals("foo", $params['username']);
    }

    /**
     * Test matching two paths that match, with multiple Path parameters.
     *
     * @return void
     */
    public function testPathMatchWithMultipleParameters()
    {
        $route = $this
            ->getMockBuilder('Sonno\Configuration\Route')
            ->disableOriginalConstructor()
            ->getMock();
        $route
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('/test/{username}/{action}'));
        $route
            ->expects($this->any())
            ->method('getHttpMethod')
            ->will($this->returnValue('GET'));

        $config = $this
            ->getMockBuilder('Sonno\Configuration\Configuration')
            ->disableOriginalConstructor()
            ->getMock();
        $config
            ->expects($this->any())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $request = $this->buildMockRequest('GET', '/test/foo/bar');

        $router = new Router($config);

        $matches = $router->match($request, $params);

        $this->assertEquals(1, count($matches));
        $this->assertEquals(2, count($params));
        $this->assertArrayHasKey('username', $params);
        $this->assertArrayHasKey('action', $params);
        $this->assertEquals("foo", $params['username']);
        $this->assertEquals("bar", $params['action']);
    }

    /**
     * Test matching two paths that match, with a RegExp-constrained Path
     * parameter.
     *
     * @return void
     */
    public function testSuccessfulPathMatchWithConstrainedParameters()
    {
        $route = $this
            ->getMockBuilder('Sonno\Configuration\Route')
            ->disableOriginalConstructor()
            ->getMock();
        $route
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('/users/user-{id: \d+}'));
        $route
            ->expects($this->any())
            ->method('getHttpMethod')
            ->will($this->returnValue('GET'));

        $config = $this
            ->getMockBuilder('Sonno\Configuration\Configuration')
            ->disableOriginalConstructor()
            ->getMock();
        $config
            ->expects($this->any())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $request = $this->buildMockRequest('GET', '/users/user-1234');

        $router = new Router($config);

        $matches = $router->match($request, $params);

        $this->assertEquals(1, count($matches));
        $this->assertEquals(1, count($params));
        $this->assertArrayHasKey('id', $params);
        $this->assertEquals("1234", $params['id']);
    }

    /**
     * Test matching two paths that match, with a RegExp-constrained Path
     * parameter.
     *
     * @return void
     */
    public function testSuccessfulPathMatchWithMultipleConstrainedParameters()
    {
        $route = $this
            ->getMockBuilder('Sonno\Configuration\Route')
            ->disableOriginalConstructor()
            ->getMock();
        $route
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('/test{type: [A-Z][0-9]+}_{user_type: new|old}/id-{id: \d+}'));
        $route
            ->expects($this->any())
            ->method('getHttpMethod')
            ->will($this->returnValue('GET'));

        $config = $this
            ->getMockBuilder('Sonno\Configuration\Configuration')
            ->disableOriginalConstructor()
            ->getMock();
        $config
            ->expects($this->any())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $request = $this->buildMockRequest('GET', '/testX11_new/id-32002');

        $router = new Router($config);

        $matches = $router->match($request, $params);

        $this->assertEquals(1, count($matches));
        $this->assertEquals(3, count($params));
        $this->assertArrayHasKey('type', $params);
        $this->assertArrayHasKey('user_type', $params);
        $this->assertArrayHasKey('id', $params);
        $this->assertEquals("X11", $params['type']);
        $this->assertEquals("new", $params['user_type']);
        $this->assertEquals("32002", $params['id']);
    }

    /**
     * Test matching two paths that match, with a RegExp-constrained Path
     * parameter, but fail because the parameter value doesn't match the
     * regular expression.
     *
     * @return void
     * @expectedException \Sonno\Http\Exception\NotFoundException
     */
    public function testFailedPathMatchWithConstrainedParameters()
    {
        $route = $this
            ->getMockBuilder('Sonno\Configuration\Route')
            ->disableOriginalConstructor()
            ->getMock();
        $route
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('/users/{id: \d+}'));

        $config = $this
            ->getMockBuilder('Sonno\Configuration\Configuration')
            ->disableOriginalConstructor()
            ->getMock();
        $config
            ->expects($this->any())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $request = $this->buildMockRequest('GET', '/users/abc');

        $router = new Router($config);

        $router->match($request, $params);
    }

    /**
     * Generate a mock \Sonno\Http\Request\RequestInterface object.
     *
     * @return \Sonno\Http\Request\RequestInterface
     */
    protected function buildMockRequest($method, $uri, $contentType = null)
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

        if ($method) {
            $request
                ->expects($this->any())
                ->method('getMethod')
                ->will($this->returnValue($method));
        }

        if ($contentType) {
            $request
                ->expects($this->any())
                ->method('getContentType')
                ->will($this->returnValue($contentType));
        }

        return $request;
    }
}
