<?php

/**
 * @category Sonno
 * @package  Test
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test;

require_once __DIR__ . '/Asset/TestResource.php';

use Sonno\Application\Application,
    Sonno\Http\Variant;

/**
 * Test class 'Application' by ensuring that the parseAnnotations() function
 * correctly returns the correct path-based data structure.
 *
 * @category Sonno
 * @package  Test
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup a text fixture.
     * Set the Response content output stream to the abyss.
     *
     * @return void
     */
    protected function setUp()
    {
        \Sonno\Http\Response\Response::setContentOutputStream('php://temp');
    }

    /**
     * Generate a mock \Sonno\Http\Request\RequestInterface object.
     *
     * @param string $method The HTTP request method.
     * @param string $uri The HTTP request URI.
     * @param string $contentType The HTTP request body content type.
     * @param string $selectedVariant The variant that the content negotiation
     *      should select.
     * @param array $queryParams The HTTP request query string parameters.
     * @param array $headers The array of HTTP header names to values.
     * @param string $contentBody The HTTP request body content.
     *
     * @return Sonno\Http\Request\RequestInterface
     */
    protected function buildMockRequest(
        $method = null,
        $uri = null,
        $contentType = null,
        $selectedVariant = null,
        $queryParams = array(),
        $headers = array(),
        $contentBody = null
    ) {
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

        if (null == $selectedVariant) {
            $request
                ->expects($this->any())
                ->method('selectVariant')
                ->will($this->returnValue(null));
        } else {
            $request
                ->expects($this->any())
                ->method('selectVariant')
                ->will($this->returnCallback(function(array $arr) {
                    if(!count($arr))
                        return null;
                    else {
                        $keys = array_keys($arr);
                        return $arr[$keys[0]];
                    }
                }));
        }

        if ($contentBody) {
            $request
                ->expects($this->any())
                ->method('getRequestBody')
                ->will($this->returnValue($contentBody));
        }

        $request
            ->expects($this->any())
            ->method('getQueryParams')
            ->will($this->returnValue($queryParams));

        $request
            ->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue($headers));

        return $request;
    }

    /**
     * Generate a mock \Sonno\Configuration\Configuration object.
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

    /**
     * Test that a 404 Not Found response is produced when a resource class
     * cannot be located to service the request.
     *
     * @return void
     */
    public function testNotFound()
    {
        $config  = $this->buildMockConfiguration();
        $request = $this->buildMockRequest();

        $app = new Application($config);
        $response = $app->run($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Test that when an exception that extends
     * Sonno\Application\WebApplicationException is thrown, the Exception's
     * constructor argument is used as the Response entity.
     *
     * @return void
     */
    public function testWebApplicationExceptionContentEntity()
    {
        $config  = $this->buildMockConfiguration();
        $request = $this->buildMockRequest();

        $app = new Application($config);
        $response = $app->run($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Test that a 405 Method Not Allowed response is produced when an incoming
     * request path matches a resource method, but not the HTTP method.
     *
     * @return void
     */
    public function testUnsupportedMethod()
    {
        $config  = $this->buildMockConfiguration(array(
            array('path' => '/test/123', 'httpMethod' => 'GET')
        ));
        $request = $this->buildMockRequest('POST', '/test/123');

        $app = new Application($config);
        $response = $app->run($request);

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertEquals('GET', $response->getHeader('Allow'));
    }

    /**
     * Test that a 406 Not Acceptable response is produced when the selected
     * resource cannot produce a representation of the type requested.
     *
     * @return void
     */
    public function testNotAcceptable()
    {
        $config  = $this->buildMockConfiguration(array(
            array(
                'path' => '/test/123',
                'httpMethod' => 'GET',
                'produces' => array()
            )
        ));
        $request = $this->buildMockRequest('GET', '/test/123');

        $app = new Application($config);
        $response = $app->run($request);

        $this->assertEquals(406, $response->getStatusCode());
    }

    /**
     * Test that the base URI is correctly considered when fulfilling a request.
     *
     * @return void
     */
    public function testBaseUri()
    {
        $config  = $this->buildMockConfiguration(array(
            array(
                'path' => '/test/123',
                'httpMethod' => 'GET',
                'produces' => array()
            )
        ), '/service/v1');
        $request = $this->buildMockRequest('GET', '/service/v1/test/123');

        $app = new Application($config);
        $response = $app->run($request);

        // expect a 406 here because the request cannot be satisfied due to
        // unacceptable content characteristics (but does match resource based
        // on URI)
        $this->assertEquals(406, $response->getStatusCode());
    }

    /**
     * Test a successful execution of Application::run() that can't interpret
     * the result of a resource method.
     *
     * @return void
     * @expectedException Sonno\Application\MalformedResourceRepresentationException
     */
    public function testMalformedResponseReturnedByResource()
    {
        $config  = $this->buildMockConfiguration(array(
            array(
                'path' => '/test/{id}',
                'httpMethod' => 'GET',
                'resourceClassName' => 'Sonno\Test\Application\Asset\TestResource',
                'resourceMethodName' => 'randomArray',
                'produces' => array('text/plain'),
                'consumes' => array('text/plain'),
                'contexts' => array())
        ), '/service/v1');
        $request = $this->buildMockRequest(
            'GET',
            '/service/v1/test/camelCase',
            'text/plain',
            new Variant(null, null, 'text/plain')
        );

        $app = new Application($config);
        $response = $app->run($request);
    }

    /**
     * Test a successful execution of Application::run() that correctly
     * processes the result of a resource method that returns a Response object.
     *
     * @return void
     */
    public function testResponseReturnedByResource()
    {
        $config  = $this->buildMockConfiguration(array(
            array(
                'path' => '/test/1234',
                'httpMethod' => 'GET',
                'resourceClassName' => 'Sonno\Test\Application\Asset\TestResource',
                'resourceMethodName' => 'randomResponse',
                'produces' => array('text/plain'),
                'consumes' => array('text/plain'),
                'contexts' => array())
        ), '/service/v1');
        $request = $this->buildMockRequest(
            'GET',
            '/service/v1/test/1234',
            'text/plain',
            new Variant('text/plain')
        );

        $app = new Application($config);
        $response = $app->run($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('random response content', $response->getContent());
    }

    /**
     * Test a successful execution of Application::run() that correctly
     * processes the result of a resource method that returns an object of type
     * Renderable.
     *
     * @return void
     */
    public function testRenderableReturnedByResource()
    {
        $config  = $this->buildMockConfiguration(array(
            array(
                'path' => '/test/polo/{colour}',
                'httpMethod' => 'GET',
                'resourceClassName' => 'Sonno\Test\Application\Asset\TestResource',
                'resourceMethodName' => 'getPolo',
                'produces' => array('text/plain'),
                'consumes' => array('text/plain'),
                'contexts' => array(),
                'pathParams' => array('colour'))
        ), '/service/v1');
        $request = $this->buildMockRequest(
            'GET',
            '/service/v1/test/polo/blue',
            'text/plain',
            new Variant(null, null, 'text/plain')
        );

        $app = new Application($config);
        $response = $app->run($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('blue', $response->getContent());
        $this->assertEquals('text/plain', $response->getHeader('Content-Type'));
    }

    /**
     * Test that a resource class method whose first argument is a type that
     * impelements Renderable will be passed an instance of that class
     * generated from the request body.
     *
     * @return void
     */
    public function testUnrenderedResourceMethodArgument()
    {
        $config  = $this->buildMockConfiguration(array(
            array(
                'path' => '/test/polo',
                'httpMethod' => 'POST',
                'resourceClassName' => 'Sonno\Test\Application\Asset\TestResource',
                'resourceMethodName' => 'createPolo',
                'produces' => array('application/xml'),
                'consumes' => array('application/json'),
                'contexts' => array())
        ), '/service/v1');
        $request = $this->buildMockRequest(
            'POST',
            '/service/v1/test/polo',
            'application/json',
            new Variant(null, null, 'application/xml'),
            array(),
            array(),
            json_encode(array('colour' => 'Magenta'))
        );

        $app = new Application($config);
        $response = $app->run($request);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('<polo><color>magenta</color></polo>', $response->getContent());
        $this->assertEquals('application/xml', $response->getHeader('Content-Type'));
    }

    /**
     * Test a successful execution of Application::run() that correctly
     * processes the result of a resource method that returns a scalar value.
     *
     * @return void
     */
    public function testScalarReturnedByResource()
    {
        $config  = $this->buildMockConfiguration(array(
            array(
                'path'               => '/test/{str}',
                'httpMethod'         => 'GET',
                'resourceClassName'  => 'Sonno\Test\Application\Asset\TestResource',
                'resourceMethodName' => 'modifyString',
                'produces'           => array('text/plain'),
                'contexts'           => array('_incomingRequest' => 'Request'),
                'pathParams'         => array('str'),
                'queryParams'        => array('op'))
        ), '/service/v1');
        $request = $this->buildMockRequest(
            'GET',
            '/service/v1/test/camelCase',
            null,
            new Variant(null, null, 'text/plain'),
            array('op' => 'upper')
        );

        $app = new Application($config);
        $response = $app->run($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("CAMELCASE|GET", $response->getContent());
        $this->assertEquals('text/plain', $response->getHeader('Content-Type'));
    }

    /**
     * Test that a cookie in the HTTP request is successfully passed to a
     * resource method call.
     *
     * @todo
     * @return void
     */
    public function testCookieParam()
    {
    }

    /**
     * Test that a cookie in the HTTP request is successfully passed to a
     * resource method call.
     *
     * @return void
     */
    public function testFormParam()
    {
        $config  = $this->buildMockConfiguration(array(
            array(
                'path'               => '/test/paper',
                'httpMethod'         => 'GET',
                'resourceClassName'  => 'Sonno\Test\Application\Asset\TestResource',
                'resourceMethodName' => 'modifyString',
                'produces'           => array('text/plain'),
                'contexts'           => array('_incomingRequest' => 'Request'),
                'formParams'         => array('str'),
                'queryParams'        => array('op'))
        ), '/service/v1');
        $request = $this->buildMockRequest(
            'GET',
            '/service/v1/test/paper',
            null,
            new Variant('text/plain'),
            array('op' => 'upper'),
            array(),
            'str=hackeysackey'
        );

        $app = new Application($config);
        $response = $app->run($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("HACKEYSACKEY|GET", $response->getContent());
    }

    /**
     * Test that a cookie in the HTTP request is successfully passed to a
     * resource method call.
     *
     * @return void
     */
    public function testHeaderParam()
    {
        $config  = $this->buildMockConfiguration(array(
            array(
                'path'               => '/test/{str}',
                'httpMethod'         => 'GET',
                'resourceClassName'  => 'Sonno\Test\Application\Asset\TestResource',
                'resourceMethodName' => 'modifyString',
                'produces'           => array('text/plain'),
                'contexts'           => array('_incomingRequest' => 'Request'),
                'headerParams'       => array('str'),
                'queryParams'        => array('op'))
        ), '/service/v1');
        $request = $this->buildMockRequest(
            'GET',
            '/service/v1/test/camelCase',
            null,
            new Variant('text/plain'),
            array('op' => 'upper'),
            array('str' => 'Cat')
        );

        $app = new Application($config);
        $response = $app->run($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("CAT|GET", $response->getContent());
    }

    /**
     * Test that a cookie in the HTTP request is successfully passed to a
     * resource method call.
     *
     * @return void
     */
    public function testPathParam()
    {
        $config  = $this->buildMockConfiguration(array(
            array(
                'path'               => '/test/{str}',
                'httpMethod'         => 'GET',
                'resourceClassName'  => 'Sonno\Test\Application\Asset\TestResource',
                'resourceMethodName' => 'modifyString',
                'produces'           => array('text/plain'),
                'contexts'           => array('_incomingRequest' => 'Request'),
                'pathParams'         => array('str'),
                'queryParams'        => array('op'))
        ), '/service/v1');
        $request = $this->buildMockRequest(
            'GET',
            '/service/v1/test/camelCase',
            null,
            new Variant('text/plain'),
            array('op' => 'upper')
        );

        $app = new Application($config);
        $response = $app->run($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("CAMELCASE|GET", $response->getContent());
    }

    /**
     * Test that a cookie in the HTTP request is successfully passed to a
     * resource method call.
     *
     * @return void
     */
    public function testQueryParam()
    {
        $config  = $this->buildMockConfiguration(array(
            array(
                'path'               => '/test/{str}',
                'httpMethod'         => 'GET',
                'resourceClassName'  => 'Sonno\Test\Application\Asset\TestResource',
                'resourceMethodName' => 'modifyString',
                'produces'           => array('text/plain'),
                'contexts'           => array('_incomingRequest' => 'Request'),
                'pathParams'         => array('str'),
                'queryParams'        => array('op'))
        ), '/service/v1');
        $request = $this->buildMockRequest(
            'GET',
            '/service/v1/test/camelCase',
            null,
            new Variant('text/plain'),
            array('op' => 'upper')
        );

        $app = new Application($config);
        $response = $app->run($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("CAMELCASE|GET", $response->getContent());
    }

    /**
     * Test a {@link Sonno\Application\WebApplicationException} being thrown
     * by a resource class method.
     *
     * @return void
     */
    public function testThrownWebApplicationException()
    {
        $config = $this->buildMockConfiguration(array(
            array(
                'path'               => '/test',
                'httpMethod'         => 'GET',
                'resourceClassName'  => 'Sonno\Test\Application\Asset\TestResource',
                'resourceMethodName' => 'causeError',
                'produces'           => array('text/plain'),
                'contexts'           => array(), // do not inject context
                'pathParams'         => array(),
                'queryParams'        => array()
            )
        ), '/service/v1');
        $request = $this->buildMockRequest(
            'GET',
            '/service/v1/test',
            null,
            new Variant('text/plain')
        );

        $app = new Application($config);
        $response = $app->run($request);

        $this->assertEquals(405, $response->getStatusCode());
    }

    /**
     * Test registering a response filter callback and ensuring it is
     * executed.
     *
     * @return void
     */
    public function testResponseFilterCallback()
    {
        $config = $this->buildMockConfiguration(array(
            array(
                'path'               => '/test',
                'httpMethod'         => 'GET',
                'resourceClassName'  => 'Sonno\Test\Application\Asset\TestResource',
                'resourceMethodName' => 'causeError',
                'produces'           => array('text/plain'),
                'contexts'           => array(), // do not inject context
                'pathParams'         => array(),
                'queryParams'        => array()
            )
        ), '/service/v1');
        $request = $this->buildMockRequest(
            'GET',
            '/service/v1/nonexistent'
        );

        $app = new Application($config);
        $app->registerResponseFilter(404, function($request, $response) {
            $response->setHeaders(array('Content-Type' => 'text/plain'));
        });
        $app->registerResponseFilter(404, function($request, $response) {
            $response->setContent('Sorry, but that resource does not exist');
        });
        $response = $app->run($request);

        $this->assertEquals('text/plain', $response->getHeader('Content-Type'));
        $this->assertEquals('Sorry, but that resource does not exist', $response->getContent());
    }

    /**
     * Test registering, and then unregistering a specific response filter
     * callback and ensure that it is not executed.
     *
     * @return void
     */
    public function testUnregisterSpecificFilterCallback()
    {
        $config = $this->buildMockConfiguration(array(
            array(
                'path'               => '/test',
                'httpMethod'         => 'GET',
                'resourceClassName'  => 'Sonno\Test\Application\Asset\TestResource',
                'resourceMethodName' => 'causeError',
                'produces'           => array('text/plain'),
                'contexts'           => array(), // do not inject context
                'pathParams'         => array(),
                'queryParams'        => array()
            )
        ), '/service/v1');
        $request = $this->buildMockRequest(
            'GET',
            '/service/v1/nonexistent'
        );

        $app = new Application($config);

        $tmpCallback = function($request, $response) {
            $response->setHeaders(array('Content-Type' => 'text/html'));
        };
        $app->registerResponseFilter(404, $tmpCallback);
        $app->registerResponseFilter(404, function($request, $response) {
            $response->setContent('Sorry, but that resource does not exist');
        });
        $app->unregisterResponseFilter(404, $tmpCallback);

        $response = $app->run($request);

        $this->assertEquals('Sorry, but that resource does not exist', $response->getContent());
    }

    /**
     * Test registering and then unregistering all response filter callbacks
     * for a specific status code, and ensure that they are not executed.
     *
     * @return void
     */
    public function testUnregisterAllFilterCallbacks()
    {
        $config = $this->buildMockConfiguration(array(
            array(
                'path'               => '/test',
                'httpMethod'         => 'GET',
                'resourceClassName'  => 'Sonno\Test\Application\Asset\TestResource',
                'resourceMethodName' => 'causeError',
                'produces'           => array('text/plain'),
                'contexts'           => array(), // do not inject context
                'pathParams'         => array(),
                'queryParams'        => array()
            )
        ), '/service/v1');
        $request = $this->buildMockRequest(
            'GET',
            '/service/v1/nonexistent'
        );

        $app = new Application($config);

        $app->registerResponseFilter(404, function($request, $response) {
            $response->setHeaders(array('Content-Type' => 'text/html'));
        });
        $app->registerResponseFilter(404, function($request, $response) {
            $response->setContent('Sorry, but that resource does not exist');
        });
        $app->unregisterResponseFilter(404);

        $response = $app->run($request);

        $this->assertNull($response->getContent());
    }
}
