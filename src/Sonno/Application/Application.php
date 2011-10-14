<?php

/**
 * @category  Sonno
 * @package   Sonno\Application
 * @author    Dave Hauenstein <davehauenstein@gmail.com>
 * @author    Tharsan Bhuvanendran <me@tharsan.com>
 * @author    360i <sonno@360i.com>
 * @copyright Copyright (c) 2011 360i LLC (http://360i.com)
 * @license   http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Application;

use ReflectionClass,
    ReflectionMethod,
    ReflectionProperty,
    Sonno\Configuration\Configuration,
    Sonno\Configuration\Route,
    Sonno\Http\Request\RequestInterface,
    Sonno\Http\Response\Response,
    Sonno\Http\Exception\NotFoundException,
    Sonno\Http\Exception\MethodNotAllowedException,
    Sonno\Http\Exception\UnsupportedMediaTypeException,
    Sonno\Http\Variant,
    Sonno\Router\Router;

/**
 * The entrypoint to Sonno for a PHP application, the Application class
 * processes an incoming HTTP request, dispatches the request to a resource
 * class method designated by Sonno\Configuration\Configuration and outputs
 * the result.
 *
 * @category Sonno
 * @package  Sonno\Application
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */
class Application
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
    public function __construct(Configuration $config)
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
     * Process an incoming request.
     * Determine the appropriate route for the request using a Router, and then
     * execute the resource method to obtain and return a result.
     *
     * @param Sonno\Http\Request\RequestInterface $request The incoming request
     * @return Sonno\Http\Response\Response
     * @throws InvalidArgumentException
     * @see Sonno\Router\Router\Router
     */
    public function run(RequestInterface $request)
    {
        if (null == $request) {
            throw new InvalidArgumentException(
                'Missing function argument: request'
            );
        }

        // attempt to find routes that match the current request
        $pathParams  = array();
        $router      = new Router($this->_config);
        try {
            $routes = $router->match($request, $pathParams);
        } catch(NotFoundException $e) {
            $response = new Response(404);
            $response->sendResponse();
            return $response;
        } catch(MethodNotAllowedException $e) {
            $response = new Response(405);
            $response->sendResponse();
            return $response;
        } catch(UnsupportedMediaTypeException $e) {
            $response = new Response(415);
            $response->sendResponse();
            return $response;
        }

        // construct a hash map of Variants based on Routes
        $variantMap = array();
        $variants   = array();
        foreach ($routes as $route) {
            $routeProduces = $route->getProduces();
            foreach ($routeProduces as $consumes) {
                $variant = new Variant(null, null, $consumes);
                $key = spl_object_hash($variant);
                $variantMap[$key] = $route;
                $variants[] = $variant;
            }
        }

        // select a Variant and find the corresponding route
        $selectedVariant = $request->selectVariant($variants);
        if (null == $selectedVariant) {
            $response = new Response(406);
            $response->sendResponse();
            return $response;
        }
        $selectedVariantHash = spl_object_hash($selectedVariant);
        $selectedRoute = $variantMap[$selectedVariantHash];

        // obtain Reflection objects for the resource method selected
        $reflClass = new ReflectionClass(
            $selectedRoute->getResourceClassName()
        );
        $reflMethod = $reflClass->getMethod(
            $selectedRoute->getResourceMethodName()
        );

        // create a flat array of method call arguments
        $resourceMethodArgs = $this->createArgumentList(
            $reflMethod,
            $selectedRoute,
            $request,
            $pathParams
        );

        // instantiate the selected resource class
        $resource = $reflClass->newInstance();

        // inject Context variables into the resource class instance
        foreach ($selectedRoute->getContexts() as $propertyName => $contextType) {
            try {
                $reflProperty = $reflClass->getProperty($propertyName);
            } catch(\ReflectionException $e) {
                continue;
            }

            $reflProperty->setAccessible(true);

            switch ($contextType) {
                case 'Request':
                    $reflProperty->setValue($resource, $request);
                    break;
            }
        }

        // execute the selected resource method using the generated method
        // arguments
        $result = $reflMethod->invokeArgs($resource, $resourceMethodArgs);

        // object is a scalar value: construct a new Response
        if (is_scalar($result)) {
            $response = new Response(
                200,
                $result,
                array('Content-Type' => $selectedVariant->getMediaType())
            );
            $response->sendResponse();
            return $response;

        // object is already a Response
        } else if ($result instanceof Response) {
            $result->sendResponse();
            return $result;

        // object implements RenderableInterface: construct a Response using the
        // reprsentation produced by render()
        } else if ($result instanceof Renderable) {
            $response = new Response(
                200,
                $result->render($selectedVariant->getMediaType()),
                array('Content-Type' => $selectedVariant->getMediaType())
            );

            $response->sendResponse();
            return $response;

        // cannot determine how to handle the object returned
        } else {
            throw new MalformedResourceRepresentationException();
        }
    }

    /**
     * Examine a class method and reorder all route parameters into a flat
     * integer-indexed array so that it can be passed as arguments to the class
     * method call.
     *
     * @param ReflectionMethod $method The class method
     * @param Sonno\Configuration\Route $route The route containing parameters
     *      to be processed.
     * @param Sonno\Request\RequestInterface $request The incoming request.
     * @return array
     * @todo Add support for cookie, header and form parameters.
     */
    protected function createArgumentList(
        ReflectionMethod $method,
        Route $route,
        RequestInterface $request,
        array $seedParams)
    {
        $resourceMethodArgs = array();

        $params = array_merge(
            $seedParams,
            $request->getQueryParams() // add Query params from the Request
        );

        $methodParameters = $method->getParameters();
        foreach ($methodParameters as $index => $reflParameter) {
            $parameterName = $reflParameter->getName();

            if (isset($params[$parameterName])) {
                $resourceMethodArgs[$index] = $params[$parameterName];
            }
        }

        return $resourceMethodArgs;
    }
}
