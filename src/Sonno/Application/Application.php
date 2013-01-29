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

use Sonno\Configuration\Configuration,
    Sonno\Http\Exception\NotAcceptableException,
    Sonno\Http\Request\RequestInterface,
    Sonno\Http\Response\Response,
    Sonno\Http\Variant,
    Sonno\Dispatcher\DispatcherInterface,
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
     * @var \Sonno\Configuration\Configuration
     */
    protected $_config;

    /**
     * Request dispatcher.
     *
     * @var \Sonno\Dispatcher\DispatcherInterface
     */
    protected $_dispatcher;

    /**
     * A registry of filters that may perform additional processing on a
     * {@link Sonno\Response\Response} before it is delivered.
     *
     * @var array
     */
    protected $_responseFilters;

    /**
     * Construct a new Application.
     *
     * @param \Sonno\Configuration\Configuration $config Resource configuration.
     */
    public function __construct(Configuration $config)
    {
        $this->_config = $config;
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
     * Setter for configuration object.
     *
     * @param  \Sonno\Configuration\Configuration $config
     * @return \Sonno\Application\Application Implements fluent interface.
     */
    public function setConfig(Configuration $config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Getter for dispatcher object.
     *
     * @return \Sonno\Dispatcher\DispatcherInterface
     */
    public function getDispatcher()
    {
        if (null === $this->_dispatcher) {
            $this->setDispatcher(new Dispatcher());
        }

        return $this->_dispatcher;
    }

    /**
     * Setter for dispatcher object.
     *
     * @param  \Sonno\Dispatcher\DispatcherInterface $dispatcher
     * @return \Sonno\Application\Application Implements fluent interface.
     */
    public function setDispatcher(DispatcherInterface $dispatcher)
    {
        $this->_dispatcher = $dispatcher;
        return $this;
    }

    /**
     * Process an incoming request.
     * Determine the appropriate route for the request using a Router, and then
     * execute the resource method to obtain and return a result.
     *
     * @param \Sonno\Http\Request\RequestInterface $request The incoming request
     * @throws MalformedResourceRepresentationException
     * @throws \Sonno\Http\Exception\NotAcceptableException
     * @return \Sonno\Http\Response\Response
     */
    public function run(RequestInterface $request)
    {
        $selectedVariant = $result = null;

        try {
            // attempt to find routes that match the current request
            $router = new Router($this->_config);
            $routes = $router->match($request, $pathParams);

            // construct a hash map of Variants based on Routes
            $variantMap = array(); // variant hash => <Sonno\Http\Variant>
            $variants   = array(); // array<Sonno\Http\Variant>

            /** @var $route \Sonno\Configuration\Route */
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
                throw new NotAcceptableException;
            }

            $selectedVariantHash = spl_object_hash($selectedVariant);
            $selectedRoute = $variantMap[$selectedVariantHash];

            // maintain URI information for resource class context injection
            $uriInfo = new UriInfo($this->_config, $request, $selectedRoute);
            $uriInfo->setPathParameters($pathParams);
            $uriInfo->setQueryParameters($request->getQueryParams());

            // execute the resource class method and obtain the result
            $dispatcher = $this->getDispatcher();
            $dispatcher->setRequest($request);
            $dispatcher->setUriInfo($uriInfo);

            $result = $dispatcher->dispatch($selectedRoute);
        } catch(WebApplicationException $e) {
            $result = $e->getResponse();
        }

        // object is a scalar value: construct a new Response
        if (is_scalar($result)) {
            $response = new Response(200, $result);

        // object is already a Response
        } else if ($result instanceof Response) {
            $response = $result;

        // object implements the Renderable interface: construct a Response
        // using the reprsentation produced by render()
        } else if ($result instanceof Renderable) {
            $response = new Response(200, $result->render($selectedVariant));

        // cannot determine how to handle the object returned
        } else {
            throw new MalformedResourceRepresentationException;
        }

        // ensure a Content-Type header is present
        if (!$response->hasHeader('Content-Type')
            && $response->getStatusCode() < 300
            && $response->getContent()
        ) {
            $response->setHeaders(
                array(
                    'Content-Type' => $selectedVariant->getMediaType()
                )
            );
        }

        // ensure a Content-Length header is present
        if (!$response->hasHeader('Content-Length')) {
            $response->setHeaders(
                array(
                    'Content-Length' => strlen($response->getContent())
                )
            );
        }

        // process any HTTP status filter callbacks
        $statusCode = $response->getStatusCode();
        if (isset($this->_responseFilters[$statusCode])) {
            foreach ($this->_responseFilters[$statusCode] as $filterCallback) {
                $filterCallback($request, $response);
            }
        }

        $response->sendResponse();
        return $response;
    }

    /**
     * Register a new response filter for a specific HTTP status code.
     *
     * @param int       $statusCode     The HTTP status code to register a filter for.
     * @param Callable  $filterCallback The PHP callback to execute when the
     *      HTTP error registered against occurs.
     *
     * @throws \InvalidArgumentException
     * @return \Sonno\Application\Application Implements fluent interface.
     */
    public function registerResponseFilter($statusCode, $filterCallback)
    {
        if (!is_callable($filterCallback)) {
            throw new \InvalidArgumentException(
                'The Filter Callback must be callable as a PHP function.'
            );
        }

        if (isset($this->_responseFilters[$statusCode])) {
            $this->_responseFilters[$statusCode][] = $filterCallback;
        } else {
            $this->_responseFilters[$statusCode] = array($filterCallback);
        }

        return $this;
    }

    /**
     * Unregister a single response filter callback, or all filter callbacks
     * for a specific status code.
     *
     * @param int $statusCode The HTTP status code to register a filter for.
     * @param Callable|null $filterCallback The PHP callback to remove from the
     *      response filter set, or NULL to remove all filters from the
     *      specified HTTP status code filter set.
     *
     * @return \Sonno\Application\Application Implements fluent interface.
     */
    public function unregisterResponseFilter(
        $statusCode,
        $filterCallback = NULL
    )
    {
        if (isset($this->_responseFilters[$statusCode])) {
            // unregister all response filters for the specified status code
            if (is_null($filterCallback)) {
                unset($this->_responseFilters[$statusCode]);

            // locate & remove the specified $filterCallback in the filter set
            } else {
                $key = array_search(
                    $filterCallback,
                    $this->_responseFilters[$statusCode]
                );

                if (false !== $key) {
                    unset($this->_responseFilters[$statusCode][$key]);
                }
            }
        }

        return $this;
    }
}
