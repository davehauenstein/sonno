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
 * {@link \Sonno\Configuration\Configuration Configuration objects} based on
 * a defined set of classes marked up with Sonno Annotations.
 *
 * @category   Sonno
 * @package    Sonno\Configuration\Driver
 * @subpackage Configuration
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 * @see        \Sonno\Configuration\Driver\DriverInterface
 * @see        \Sonno\Configuration\Configuration
 */
class AnnotationDriver implements DriverInterface
{
    /**
     * A local instance of a Configuration object.
     *
     * @var \Sonno\Configuration\Configuration
     */
    protected $_config;

    /**
     * A local instance of an Annotation Reader object.
     *
     * @var \Sonno\Annotation\Reader\ReaderInterface
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
     * @param  \Sonno\Annotation\Reader\ReaderInterface $reader
     */
    public function __construct(array $classes, ReaderInterface $reader = null)
    {
        $this->setAnnotatedResources($classes);
        $reader !== null && $this->setReader($reader);
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
     * Setter for annotation reader object.
     *
     * @param  \Sonno\Annotation\Reader\ReaderInterface $reader
     * @return \Sonno\Configuration\AnnotationDriver Implements fluent
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
     * @return \Sonno\Annotation\Reader\ReaderInterface
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
     * @return \Sonno\Configuration\Driver\AnnotationDriver Implements a
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
     * {@link \Sonno\Configuration\Configuration Configuration} instance.
     *
     * @return \Sonno\Configuration\Configuration
     * @throws \Sonno\Configuration\Driver\DriverException If no annotation
     *         reader object is set.
     * @see    \Sonno\Configuration\Driver\DriverInterface
     */
    public function parseConfig()
    {
        if (!($reader = $this->getReader())) {
            throw new DriverException(
                'Could not parse annotation configuration because
                 no annotation reader object has been set.'
            );
        }

        $resources = $this->getAnnotatedResources();
        $config    = $this->getConfig();

        foreach ($resources as $resourceClass) {
            $class       = new ReflectionClass($resourceClass);
            $classParams = $this->_extractClassParams($class, $reader);

            $properties = $class->getProperties();
            foreach ($properties as $property) {
                // Class and property params don't overlap, just merge.
                $params = array_merge(
                    $classParams,
                    $this->_extractPropertyParams($property, $reader)
                );
            }

            $methods = $class->getMethods();
            foreach ($methods as $method) {
                $params['path'] = $classParams['path'];
                // Class and method params cannot just be merged because they
                // overlap. Different rules apply to the ways these have to be
                // merged and that's handled in _extractMethodParams.
                $params = $this->_extractMethodParams(
                    $method,
                    $reader,
                    $params
                );
                $config->addRoute(new Route($params));
            }
        }

        return $config;
    }

    /**
     * Return a list of Route parameters based on class annotations.
     *
     * @param  \ReflectionClass $class
     * @param  \Sonno\Annotation\Reader\ReaderInterface $reader
     * @return array Class annotations.
     */
    protected function _extractClassParams(
        ReflectionClass $class,
        ReaderInterface $reader
    )
    {
        if (!($path = $reader->getClassAnnotation($class, '\Sonno\Annotation\Path'))) {
            $path = new \Sonno\Annotation\Path('');
        }
        if (!($consumes = $reader->getClassAnnotation($class, '\Sonno\Annotation\Consumes'))) {
            $consumes = new \Sonno\Annotation\Consumes();
        }
        if (!($produces = $reader->getClassAnnotation($class, '\Sonno\Annotation\Produces'))) {
            $produces = new \Sonno\Annotation\Produces();
        }

        return array(
            'resourceClassName' => $class->getName(),
            'path'              => $path->getPath(),
            'consumes'          => $consumes->getMediaTypes(),
            'produces'          => $produces->getMediaTypes(),
        );
    }

    /**
     * Return a list of Route parameters based on method annotations.
     *
     * @param  \ReflectionMethod $method
     * @param  \Sonno\Annotation\Reader\ReaderInterface $reader
     * @param  array $params Merge method parameters into this.
     * @todo   Add support for the following annotations:
     *            - Context
     *            - DefaultValue
     */
    protected function _extractMethodParams(
        ReflectionMethod $method,
        ReaderInterface $reader,
        array $params = array()
    )
    {
        $params['resourceMethodName'] = $method->getName();

        if ($annot = $reader->getMethodAnnotation($method, '\Sonno\Annotation\Path')) {
            $params['path'] = isset($params['path'])
                ? $params['path'] . $annot->getPath()
                : $annot->getPath();
        }
        if ($annot = $reader->getMethodAnnotation($method, '\Sonno\Annotation\Consumes')) {
            $params['consumes'] = $annot->getMediaTypes();
        }
        if ($annot = $reader->getMethodAnnotation($method, '\Sonno\Annotation\Produces')) {
            $params['produces'] = $annot->getMediaTypes();
        }
        if ($annot = $reader->getMethodAnnotation($method, '\Sonno\Annotation\HttpMethod')) {
            $params['httpMethod'] = (string) $annot;
        }
        if ($annot = $reader->getMethodAnnotation($method, '\Sonno\Annotation\PathParam')) {
            $params['pathParams'] = $annot->getParams();
        }
        if ($annot = $reader->getMethodAnnotation($method, '\Sonno\Annotation\CookieParam')) {
            $params['cookieParams'] = $annot->getParams();
        }
        if ($annot = $reader->getMethodAnnotation($method, '\Sonno\Annotation\FormParam')) {
            $params['formParams'] = $annot->getParams();
        }
        if ($annot = $reader->getMethodAnnotation($method, '\Sonno\Annotation\HeaderParam')) {
            $params['headerParams'] = $annot->getParams();
        }
        if ($annot = $reader->getMethodAnnotation($method, '\Sonno\Annotation\QueryParam')) {
            $params['queryParam'] = $annot->getParams();
        }

        return $params;
    }

    /**
     * Return a list of Route parameters based on property annotations.
     *
     * @param  \ReflectionProperty $property
     * @param  \Sonno\Annotation\Reader\ReaderInterface $reader
     */
    public function _extractPropertyParams(
        ReflectionProperty $property,
        ReaderInterface $reader
    )
    {
        $params = array();
        if ($annot = $reader->getPropertyAnnotation($property, '\Sonno\Annotation\Context')) {
            $params['contexts'] = array(
                $property->getName() => $annot->getContext()
            );
        }
        return $params;
    }
}
