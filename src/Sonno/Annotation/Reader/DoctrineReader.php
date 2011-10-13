<?php

/**
 * @category   Sonno
 * @package    Sonno\Annotation\Reader
 * @subpackage Reader
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 * @author     Tharsan Bhuvanendran <me@tharsan.com>
 * @author     360i <sonno@360i.com>
 * @copyright  Copyright (c) 2011 360i LLC (http://360i.com)
 * @license    http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Annotation\Reader;

use \ReflectionClass,
    \ReflectionMethod,
    \ReflectionProperty,
    \Sonno\Annotation\Reader\ReaderInterface,
    \Doctrine\Common\Annotations\Reader;

/**
 * An class that proxies annotation reading to Doctrine's reader.
 *
 * @category   Sonno
 * @package    Sonno\Annotation\Reader
 * @subpackage Reader
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 * @see        Sonno\Annotation\Reader\ReaderInterface
 */
class DoctrineReader implements ReaderInterface
{
    /**
     * An instance of a Doctrine Annotation reader.
     *
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $_reader;

    /**
     * Construct a new DoctrineReader.
     *
     * @param \Doctrine\Common\Annotations\Reader $reader A Doctrine annotation
     *        reader instance.
     */
    public function __construct(Reader $reader)
    {
        $this->setReader($reader);
    }

    /**
     * Retrieve a list of class level annotations.
     *
     * @param  $class \ReflectionClass Class to retrieve annotations from.
     * @throws \Sonno\Annotation\Reader\ReaderException No reader set.
     */
    public function getClassAnnotations(ReflectionClass $class)
    {
        if (null === ($reader = $this->getReader())) {
            throw new ReaderException('No reader set.');
        }
        return $reader->getClassAnnotations($class);
    }

    /**
     * Retrieve a class level annotation based on annotation name.
     *
     * @param $class \ReflectionClass Class to retrieve annotations from.
     * @param $annotationName string The name of the annotation to retrieve.
     * @throws \Sonno\Annotation\Reader\ReaderException No reader set.
     */
    public function getClassAnnotation(ReflectionClass $class, $annotationName)
    {
        if (null === ($reader = $this->getReader())) {
            throw new ReaderException('No reader set.');
        }
        return $reader->getClassAnnotation($class, $annotationName);
    }

    /**
     * Retrieve a list of method level annotations.
     *
     * @param $method \ReflectionMethod Method to retrieve annotations from.
     * @throws \Sonno\Annotation\Reader\ReaderException No reader set.
     */
    public function getMethodAnnotations(ReflectionMethod $method)
    {
        if (null === ($reader = $this->getReader())) {
            throw new ReaderException('No reader set.');
        }
        return $reader->getMethodAnnotations($method);
    }

    /**
     * Retrieve a method level annotation based on annotation name.
     *
     * @param $method \ReflectionMethod Method to retrieve annotations from.
     * @param $annotationName string The name of the annotation to retrieve.
     * @throws \Sonno\Annotation\Reader\ReaderException No reader set.
     */
    public function getMethodAnnotation(
        ReflectionMethod $method,
        $annotationName
    )
    {
        if (null === ($reader = $this->getReader())) {
            throw new ReaderException('No reader set.');
        }
        return $reader->getMethodAnnotation($method, $annotationName);
    }

    /**
     * Retrieve a list of property level annotations.
     *
     * @param $property \ReflectionProperty Property to retrieve
     *        annotations from.
     * @throws \Sonno\Annotation\Reader\ReaderException No reader set.
     */
    public function getPropertyAnnotations(ReflectionProperty $property)
    {
        if (null === ($reader = $this->getReader())) {
            throw new ReaderException('No reader set.');
        }
        return $reader->getPropertyAnnotations($property);
    }

    /**
     * Retrieve a property level annotation based on annotation name.
     *
     * @param $property \ReflectionProperty Property to retrieve
     *        annotations from.
     * @param $annotationName string The name of the annotation to retrieve.
     * @throws \Sonno\Annotation\Reader\ReaderException No reader set.
     */
    public function getPropertyAnnotation(
        ReflectionProperty $property,
        $annotationName
    )
    {
        if (null === ($reader = $this->getReader())) {
            throw new ReaderException('No reader set.');
        }
        return $reader->getPropertyAnnotation($property, $annotationName);
    }

    /**
     * Set the Doctrine annotation reader.
     *
     * @param  \Doctrine\Common\Annotations\Reader $reader A Doctrine
     *         annotation reader instance.
     * @return \Sonno\Annotation\Reader\DoctrineReader Implement a 
     *         fluent interface.
     */
    public function setReader(Reader $reader)
    {
        $this->_reader = $reader;
        return $this;
    }

    /**
     * Get instance Doctrine annotation reader.
     *
     * @return \Sonno\Annotation\Reader\DoctrineReader Implement a 
     *         fluent interface.
     */
    public function getReader()
    {
        return $this->_reader;
    }
}
