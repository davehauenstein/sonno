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
     * A function callback responsible for the instantiation of a resource
     * class.
     *
     * @var callback
     */
    protected $_resourceCreationFunction;

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
     * @return Sonno\Configuration\AnnotationDriver Implements fluent interface.
     */
    public function setConfig(Configuration $config)
    {
        $this->_config = $config;
        return $this;
    }

    public function setResourceCreationFunction($resourceCreationFunction)
    {
        $this->_resourceCreationFunction = $resourceCreationFunction;
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
        $result = $this->_executeResource(
            $selectedRoute->getResourceClassName(),
            $selectedRoute->getResourceMethodName(),
            $selectedRoute,
            $uriInfo,
            array(
                'Request' => $request,
                'UriInfo' => $uriInfo,
            )
        );

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

    /**
     * Create an instance of a resource class, execute a class method and
     * return the result.
     *
     * @param $className string The class name.
     * @param $methodName string The class' method name.
     * @param $route Sonno\Configuration\Route The matched route.
     * @param $uriInfo Sonno\Uri\UriInfo Information about the URI.
     * @param $contextInjections array A map of context variables that can be
     *                                 injected into the resource class prior to
     *                                 execution.
     * @return mixed
     */
    protected function _executeResource(
        $className,
        $methodName,
        $route,
        $uriInfo,
        $contextInjections = array())
    {
        // obtain Reflection objects for the resource method selected
        $reflClass  = new ReflectionClass($className);
        $reflMethod = $reflClass->getMethod($methodName);

        // instantiate the selected resource class
        $resource = $this->_createResourceInstance($className);

        // construct a flat array of method arguments for the resource method
        $pathParamValues     = $uriInfo->getPathParameters();
        $queryParamValues    = $uriInfo->getQueryParameters();
        $pathParams          = $route->getPathParams() ?: array();
        $queryParams         = $route->getQueryParams() ?: array();
        $resourceMethodArgs  = array();

        foreach ($reflMethod->getParameters() as $idx => $reflParam) {
            $parameterName = $reflParam->getName();

            // search for an argument value in the Path parameter collection
            if (in_array($parameterName, $pathParams)
                && isset($pathParamValues[$parameterName])
            ) {
                $resourceMethodArgs[$idx] = $pathParamValues[$parameterName];
            }

            // search for an argument value in the Query parameter collection
            if (in_array($parameterName, $queryParams)
                && isset($queryParamValues[$parameterName])
            ) {
                $resourceMethodArgs[$idx] = $queryParamValues[$parameterName];
            }
        }

        // inject Context variables into the resource class instance
        foreach ($route->getContexts() as $propertyName => $contextType) {
            try {
                $reflProperty = $reflClass->getProperty($propertyName);
            } catch(\ReflectionException $e) {
                continue;
            }

            $reflProperty->setAccessible(true);

            if (isset($contextInjections[$contextType])) {
                $reflProperty->setValue(
                    $resource,
                    $contextInjections[$contextType]
                );
            }
        }

        // execute the selected resource method using the generated method
        // arguments
        return $reflMethod->invokeArgs($resource, $resourceMethodArgs);
    }

    /**
     * Instantiate a named class, and return the instance.
     * Uses the Application's custom resource creation function, if one is
     * available.
     * Otherwise, invokes the resource class' default constructor.
     *
     * @param $className string The name of the class to instantiate.
     * @return object
     */
    protected function _createResourceInstance($className)
    {
        return is_callable($this->_resourceCreationFunction)
            ? call_user_func($this->_resourceCreationFunction, $className)
            : new $className;
    }
}
