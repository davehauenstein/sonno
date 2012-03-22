<?php

/**
 * @category   Sonno
 * @package    Sonno\Configuration\Driver
 * @subpackage Configuration
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 * @author     Tharsan Bhuvanendran <me@tharsan.com>
 * @author     360i <sonno@360i.com>
 * @copyright  Copyright (c) 2011 360i LLC (http://360i.com)
 * @license    http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Configuration\Driver;

use Sonno\Configuration\Driver\DriverInterface,
    Sonno\Configuration\Driver\DriverException,
    Sonno\Configuration\Configuration,
    Sonno\Configuration\Route,
    Sonno\Annotation\Reader\ReaderInterface,
    ReflectionClass,
    ReflectionMethod,
    ReflectionProperty;

/**
 * A configuration driver used for generating
 * {@link Sonno\Configuration\Configuration Configuration objects} based on
 * a defined set of classes marked up with Sonno Annotations.
 *
 * @category   Sonno
 * @package    Sonno\Configuration\Driver
 * @subpackage Configuration
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 * @see        Sonno\Configuration\Driver\DriverInterface
 * @see        Sonno\Configuration\Configuration
 *
 * @todo Cleanup the _extract* methods to eliminate phpcs line length warnings.
 */
class AnnotationDriver implements DriverInterface
{
    /**
     * A local instance of a Configuration object.
     *
     * @var Sonno\Configuration\Configuration
     */
    protected $_config;

    /**
     * A local instance of an Annotation Reader object.
     *
     * @var Sonno\Annotation\Reader\ReaderInterface
     */
    protected $_reader;

    /**
     * A list of classes to parse annotations on.
     *
     * @var array A list of fully qualified resource class paths to parse to
     *      generate a configuration object.
     */
    protected $_annotatedResources = array();

    /**
     * Construct a new AnnotationDriver object.
     *
     * @param  array $classes List of classes that are annotated as Resources.
     * @param  Sonno\Annotation\Reader\ReaderInterface $reader
     */
    public function __construct(array $classes, ReaderInterface $reader = null)
    {
        $this->setAnnotatedResources($classes);
        $reader !== null && $this->setReader($reader);
    }

    /**
     * Setter for configuration object.
     *
     * @param  Sonno\Configuration\Configuration $config
     * @return Sonno\Configuration\Driver\AnnotationDriver Implements fluent
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
     * Setter for annotation reader object.
     *
     * @param  Sonno\Annotation\Reader\ReaderInterface $reader
     * @return Sonno\Configuration\Driver\AnnotationDriver Implements fluent
     *         interface.
     */
    public function setReader(ReaderInterface $reader)
    {
        $this->_reader = $reader;
        return $this;
    }

    /**
     * Getter for annotation reader object.
     *
     * @return Sonno\Annotation\Reader\ReaderInterface
     */
    public function getReader()
    {
        return $this->_reader;
    }

    /**
     * Setter for _annotatedResources property.
     *
     * @param  $classes array An array of fully qualified class names which are
     *         annotated with Sonno annotations.
     * @return Sonno\Configuration\Driver\AnnotationDriver Implements a
     *         fluent interface.
     */
    public function setAnnotatedResources(array $classes)
    {
        $this->_annotatedResources = $classes;
        return $this;
    }

    /**
     * Getter for _annotatedResources property.
     *
     * @return array An array of fully qualified class names which are
     *         annotated with Sonno annotations.
     */
    public function getAnnotatedResources()
    {
        return $this->_annotatedResources;
    }

    /**
     * A method that must be implemented by all config drivers that is used
     * to generate a Configuration object by parsing some type of configuration
     * (YAML, XML, Annotations, etc..). It must return a 
     * {@link Sonno\Configuration\Configuration Configuration} instance.
     *
     * @return Sonno\Configuration\Configuration
     * @throws Sonno\Configuration\Driver\DriverException If no annotation
     *         reader object is set.
     * @see    Sonno\Configuration\Driver\DriverInterface
     */
    public function parseConfig()
    {
        if (!$this->_reader) {
            throw new DriverException(
                'Could not parse annotation configuration because
                 no annotation reader object has been set.'
            );
        }

        $config = $this->getConfig();

        foreach ($this->_annotatedResources as $resourceClass) {
            $class       = new ReflectionClass($resourceClass);
            $classParams = $this->_extractClassParams($class);

            $properties = $class->getProperties();
            foreach ($properties as $property) {
                $classParams = array_merge_recursive(
                    $classParams,
                    $this->_extractPropertyParams($property)
                );
            }

            $methods = $class->getMethods();
            foreach ($methods as $method) {
                $methodParams = $this->_extractMethodParams($method);

                if (false !== $methodParams) {
                    $routeParams = array_merge($classParams, $methodParams);
                    $config->addRoute(new Route($routeParams));
                }
            }
        }

        return $config;
    }

    /**
     * Return a list of Route parameters based on class annotations.
     *
     * @param  ReflectionClass $class
     * @return array Class annotations.
     */
    protected function _extractClassParams(ReflectionClass $class)
    {
        $params = array('resourceClassName' => $class->getName());

        // Examine Annotation: @Path
        $annot = $this->_reader->getClassAnnotation(
            $class,
            '\Sonno\Annotation\Path'
        );
        if ($annot) {
            $params['classPath'] = $annot->getPath();
        }

        // Examine Annotation: @Consumes
        $annot = $this->_reader->getClassAnnotation(
            $class,
            '\Sonno\Annotation\Consumes'
        );
        if ($annot) {
            $params['consumes'] = $annot->getMediaTypes();
        }

        // Examine Annotation: @Produces
        $annot = $this->_reader->getClassAnnotation(
            $class,
            '\Sonno\Annotation\Produces'
        );
        if ($annot) {
            $params['produces'] = $annot->getMediaTypes();
        }

        return $params;
    }

    /**
     * Return a list of Route parameters based on method annotations.
     *
     * @param  ReflectionMethod $method
     * @return array Method annotations.
     */
    protected function _extractMethodParams(ReflectionMethod $method)
    {
        $params = array('resourceMethodName' => $method->getName());

        // Examine Annotation: @{HTTP_VERB}
        $annot = $this->_reader->getMethodAnnotation(
            $method,
            '\Sonno\Annotation\HttpMethod'
        );
        if ($annot) {
            $params['httpMethod'] = (string) $annot;
        } else {
            return false;
        }

        // Examine Annotation: @Path
        $annot = $this->_reader->getMethodAnnotation(
            $method,
            '\Sonno\Annotation\Path'
        );
        if ($annot) {
            $params['methodPath'] = $annot->getPath();
        }

        // Examine Annotation: @Consumes
        $annot = $this->_reader->getMethodAnnotation(
            $method,
            '\Sonno\Annotation\Consumes'
        );
        if ($annot) {
            $params['consumes'] = $annot->getMediaTypes();
        }

        // Examine Annotation: @Produces
        $annot = $this->_reader->getMethodAnnotation(
            $method,
            '\Sonno\Annotation\Produces'
        );
        if ($annot) {
            $params['produces'] = $annot->getMediaTypes();
        }

        // Examine Annotation: @PathParam
        $annot = $this->_reader->getMethodAnnotation(
            $method,
            '\Sonno\Annotation\PathParam'
        );
        if ($annot) {
            $params['pathParams'] = $annot->getParams();
        }

        // Examine Annotation: @CookieParam
        $annot = $this->_reader->getMethodAnnotation(
            $method,
            '\Sonno\Annotation\CookieParam'
        );
        if ($annot) {
            $params['cookieParams'] = $annot->getParams();
        }

        // Examine Annotation: @FormParam
        $annot = $this->_reader->getMethodAnnotation(
            $method,
            '\Sonno\Annotation\FormParam'
        );
        if ($annot) {
            $params['formParams'] = $annot->getParams();
        }

        // Examine Annotation: @HeaderParam
        $annot = $this->_reader->getMethodAnnotation(
            $method,
            '\Sonno\Annotation\HeaderParam'
        );
        if ($annot) {
            $params['headerParams'] = $annot->getParams();
        }

        // Examine Annotation: @QueryParam
        $annot = $this->_reader->getMethodAnnotation(
            $method,
            '\Sonno\Annotation\QueryParam'
        );
        if ($annot) {
            $params['queryParams'] = $annot->getParams();
        }

        return $params;
    }

    /**
     * Return a list of Route parameters based on property annotations.
     *
     * @param ReflectionProperty $property
     * @return array Property annotations.
     */
    public function _extractPropertyParams(ReflectionProperty $property)
    {
        $params = array();

        // Examine Annotation: @Context
        $annot = $this->_reader->getPropertyAnnotation(
            $property,
            '\Sonno\Annotation\Context'
        );
        if ($annot) {
            $params['contexts'] = array(
                $property->getName() => $annot->getContext()
            );
        }

        return $params;
    }
}
