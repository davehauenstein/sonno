<?php

/**
 * @category Sonno
 * @package  Sonno\Test\Dispatcher
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Dispatcher;

use Sonno\Dispatcher\Dispatcher;

/**
 * Test the default Dispatcher class provided as part of Sonno.
 *
 * @category Sonno
 * @package  Test
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */
class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that the UriInfo object is correctly injected into the resource class.
     *
     * @Test
     */
    public function testUriInfoInjection()
    {
        $request = $this->getMockBuilder('Sonno\Http\Request\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $uriInfo = $this->getMockBuilder('Sonno\Uri\UriInfo')
            ->disableOriginalConstructor()
            ->getMock();

        $uriInfo->expects($this->any())
            ->method('getAbsolutePath')
            ->will($this->returnValue('/test/injection/uriinfo'));

        $route = $this->getMockBuilder('Sonno\Configuration\Route')
            ->disableOriginalConstructor()
            ->getMock();

        $route->expects($this->any())
            ->method('getResourceClassName')
            ->will($this->returnValue('Sonno\Test\Dispatcher\Asset\TestResource'));

        $route->expects($this->any())
            ->method('getResourceMethodName')
            ->will($this->returnValue('testUriInfoInjection'));

        $route->expects($this->any())
            ->method('getContexts')
            ->will($this->returnValue(array('_uriInfo' => 'UriInfo')));

        $dispatcher = new Dispatcher($request, $uriInfo);
        $result = $dispatcher->dispatch($route);

        $this->assertEquals('/test/injection/uriinfo', $result);
    }

    /**
     * Test that the Request object is correctly injected into the resource class.
     *
     * @Test
     */
    public function testRequestInjection()
    {
        $request = $this->getMockBuilder('Sonno\Http\Request\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getRequestUri')
            ->will($this->returnValue('/test/injection/request'));

        $uriInfo = $this->getMockBuilder('Sonno\Uri\UriInfo')
            ->disableOriginalConstructor()
            ->getMock();

        $route = $this->getMockBuilder('Sonno\Configuration\Route')
            ->disableOriginalConstructor()
            ->getMock();

        $route->expects($this->any())
            ->method('getResourceClassName')
            ->will($this->returnValue('Sonno\Test\Dispatcher\Asset\TestResource'));

        $route->expects($this->any())
            ->method('getResourceMethodName')
            ->will($this->returnValue('testRequestInjection'));

        $route->expects($this->any())
            ->method('getContexts')
            ->will($this->returnValue(array('_request' => 'Request')));

        $dispatcher = new Dispatcher($request, $uriInfo);
        $result = $dispatcher->dispatch($route);

        $this->assertEquals('/test/injection/request', $result);
    }

    /**
     * Test that a resource class method whose first argument is a type that
     * impelements Renderable will be passed an instance of that class
     * generated from the request body.
     *
     * @Test
     */
    public function testUnrenderedObjectArgument()
    {
        $request = $this->getMockBuilder('Sonno\Http\Request\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $requestBody = array('colour' => 'blue', 'brand' => 'aeropostale');
        $request->expects($this->any())
            ->method('getRequestBody')
            ->will($this->returnValue(json_encode($requestBody)));

        $uriInfo = $this->getMockBuilder('Sonno\Uri\UriInfo')
            ->disableOriginalConstructor()
            ->getMock();

        $route = $this->getMockBuilder('Sonno\Configuration\Route')
            ->disableOriginalConstructor()
            ->getMock();

        $route->expects($this->any())
            ->method('getResourceClassName')
            ->will($this->returnValue('Sonno\Test\Dispatcher\Asset\TestResource'));

        $route->expects($this->any())
            ->method('getResourceMethodName')
            ->will($this->returnValue('testUnrenderedObjectArgument'));

        $route->expects($this->any())
            ->method('getContexts')
            ->will($this->returnValue(array()));

        $dispatcher = new Dispatcher($request, $uriInfo);
        $result = $dispatcher->dispatch($route);

        $this->assertEquals('colour=blue&brand=aeropostale', $result);
    }

    /**
     * Test that a Path argument is correctly passed to the resource method.
     *
     * @Test
     */
    public function testPathArgument()
    {
        $request = $this->getMockBuilder('Sonno\Http\Request\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $uriInfo = $this->getMockBuilder('Sonno\Uri\UriInfo')
            ->disableOriginalConstructor()
            ->getMock();

        $uriInfo->expects($this->any())
            ->method('getPathParameters')
            ->will($this->returnValue(array('user' => 'gollum')));

        $route = $this->getMockBuilder('Sonno\Configuration\Route')
            ->disableOriginalConstructor()
            ->getMock();

        $route->expects($this->any())
            ->method('getResourceClassName')
            ->will($this->returnValue('Sonno\Test\Dispatcher\Asset\TestResource'));

        $route->expects($this->any())
            ->method('getResourceMethodName')
            ->will($this->returnValue('testPathArgument'));

        $route->expects($this->any())
            ->method('getContexts')
            ->will($this->returnValue(array()));

        $route->expects($this->any())
            ->method('getPathParams')
            ->will($this->returnValue(array('user')));

        $dispatcher = new Dispatcher($request, $uriInfo);
        $result = $dispatcher->dispatch($route);

        $this->assertEquals('gollum', $result);
    }

    /**
     * Test that a query string argument is correctly passed to the resource method.
     *
     * @Test
     */
    public function testQueryArgument()
    {
        $request = $this->getMockBuilder('Sonno\Http\Request\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $uriInfo = $this->getMockBuilder('Sonno\Uri\UriInfo')
            ->disableOriginalConstructor()
            ->getMock();

        $uriInfo->expects($this->any())
            ->method('getQueryParameters')
            ->will($this->returnValue(array('user' => 'gollum')));

        $route = $this->getMockBuilder('Sonno\Configuration\Route')
            ->disableOriginalConstructor()
            ->getMock();

        $route->expects($this->any())
            ->method('getResourceClassName')
            ->will($this->returnValue('Sonno\Test\Dispatcher\Asset\TestResource'));

        $route->expects($this->any())
            ->method('getResourceMethodName')
            ->will($this->returnValue('testQueryArgument'));

        $route->expects($this->any())
            ->method('getContexts')
            ->will($this->returnValue(array()));

        $route->expects($this->any())
            ->method('getQueryParams')
            ->will($this->returnValue(array('user')));

        $dispatcher = new Dispatcher($request, $uriInfo);
        $result = $dispatcher->dispatch($route);

        $this->assertEquals('gollum', $result);
    }

    /**
     * Test that a request header argument is correctly passed to the resource method.
     *
     * @Test
     */
    public function testHeaderArgument()
    {
        $request = $this->getMockBuilder('Sonno\Http\Request\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue(array('ApiUser' => 'gollum')));

        $uriInfo = $this->getMockBuilder('Sonno\Uri\UriInfo')
            ->disableOriginalConstructor()
            ->getMock();

        $uriInfo->expects($this->any())
            ->method('getQueryParameters')
            ->will($this->returnValue(array('user' => 'gollum')));

        $route = $this->getMockBuilder('Sonno\Configuration\Route')
            ->disableOriginalConstructor()
            ->getMock();

        $route->expects($this->any())
            ->method('getResourceClassName')
            ->will($this->returnValue('Sonno\Test\Dispatcher\Asset\TestResource'));

        $route->expects($this->any())
            ->method('getResourceMethodName')
            ->will($this->returnValue('testHeaderArgument'));

        $route->expects($this->any())
            ->method('getContexts')
            ->will($this->returnValue(array()));

        $route->expects($this->any())
            ->method('getHeaderParams')
            ->will($this->returnValue(array('ApiUser')));

        $dispatcher = new Dispatcher($request, $uriInfo);
        $result = $dispatcher->dispatch($route);

        $this->assertEquals('gollum', $result);
    }

    /**
     * Test that a form variable argument is correctly passed to the resource method.
     *
     * @Test
     */
    public function testFormArgument()
    {
        $request = $this->getMockBuilder('Sonno\Http\Request\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getRequestBody')
            ->will($this->returnValue('user=gollum&password=th3ring'));

        $uriInfo = $this->getMockBuilder('Sonno\Uri\UriInfo')
            ->disableOriginalConstructor()
            ->getMock();

        $uriInfo->expects($this->any())
            ->method('getQueryParameters')
            ->will($this->returnValue(array('user' => 'gollum')));

        $route = $this->getMockBuilder('Sonno\Configuration\Route')
            ->disableOriginalConstructor()
            ->getMock();

        $route->expects($this->any())
            ->method('getResourceClassName')
            ->will($this->returnValue('Sonno\Test\Dispatcher\Asset\TestResource'));

        $route->expects($this->any())
            ->method('getResourceMethodName')
            ->will($this->returnValue('testFormArgument'));

        $route->expects($this->any())
            ->method('getContexts')
            ->will($this->returnValue(array()));

        $route->expects($this->any())
            ->method('getFormParams')
            ->will($this->returnValue(array('user')));

        $dispatcher = new Dispatcher($request, $uriInfo);
        $result = $dispatcher->dispatch($route);

        $this->assertEquals('gollum', $result);
    }
}