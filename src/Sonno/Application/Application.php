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
    Sonno\Http\Variant,
    Sonno\Dispatcher\Dispatcher,
    Sonno\Router\Router,
    Sonno\Uri\UriInfo;

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
     * @var Sonno\Configuration\Configuration
     */
    protected $_config;

    /**
     * Construct a new Application.
     *
     * @param Sonno\Configuration\Configuration $config Resource
     *                                                  configuration data.
     */
    public function __construct(Configuration $config)
    {
        $this->_config  = $config;
    }

    /**
     * Getter for configuration object.
     *
     * @return Sonno\Configuration\Configuration
     */
    public function getConfig()
    {
        if (null === $this->_config) {
            $this->setConfig(new Configuration());
        }

        return $this->_config;
    }

    /**
     * Setter for configuration object.
     *
     * @param  Sonno\Configuration\Configuration $config
     * @return Sonno\Configuration\Driver\AnnotationDriver Implements fluent
    *       interface.
     */
    public function setConfig(Configuration $config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Process an incoming request.
     * Determine the appropriate route for the request using a Router, and then
     * execute the resource method to obtain and return a result.
     *
     * @param Sonno\Http\Request\RequestInterface $request The incoming request
     * @return Sonno\Http\Response\Response
     * @throws InvalidArgumentException
     *
     * @see Sonno\Router\Router\Router
     */
    public function run(RequestInterface $request)
    {
        // attempt to find routes that match the current request
        $pathParams  = array();
        $router      = new Router($this->_config);
        try {
            $routes = $router->match($request, $pathParams);
        } catch(WebApplicationException $e) {
            $response = $e->getResponse();
            $response->sendResponse();
            return $response;
        }

        // construct a hash map of Variants based on Routes
        $variantMap = array(); // variant hash => <Sonno\Http\Variant>
        $variants   = array(); // array<Sonno\Http\Variant>
        foreach ($routes as $route) {
            $routeProduces = $route->getProduces();
            foreach ($routeProduces as $produces) {
                $variant = new Variant(null, null, $produces);
                $variantHash = spl_object_hash($variant);
                $variantMap[$variantHash] = $route;
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

        // maintain URI information for resource class context injection
        $uriInfo = new UriInfo($this->_config, $request, $selectedRoute);
        $uriInfo->setPathParameters($pathParams);
        $uriInfo->setQueryParameters($request->getQueryParams());

        // execute the resource class method and obtain the result
        $dispatcher = new Dispatcher($request, $uriInfo);
        $result = $dispatcher->dispatch($selectedRoute);

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

        // object implements the Renderable interface: construct a Response
        // using the reprsentation produced by render()
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
}
