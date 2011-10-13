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

use ReflectionClass,
    ReflectionMethod,
    ReflectionProperty;

/**
 * Contract for annotation readers.
 *
 * @category   Sonno
 * @package    Sonno\Annotation\Reader
 * @subpackage Reader
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 */
interface ReaderInterface
{
    /**
     * Retrieve a list of class level annotations.
     *
     * @param  $class \ReflectionClass Class to retrieve annotations from.
     */
    public function getClassAnnotations(ReflectionClass $class);

    /**
     * Retrieve a class level annotation based on annotation name.
     *
     * @param $class \ReflectionClass Class to retrieve annotations from.
     * @param $annotationName string The name of the annotation to retrieve.
     */
    public function getClassAnnotation(
        ReflectionClass $class,
        $annotationName
    );

    /**
     * Retrieve a list of method level annotations.
     *
     * @param $method \ReflectionMethod Method to retrieve annotations from.
     */
    public function getMethodAnnotations(ReflectionMethod $method);

    /**
     * Retrieve a method level annotation based on annotation name.
     *
     * @param $method \ReflectionMethod Method to retrieve annotations from.
     * @param $annotationName string The name of the annotation to retrieve.
     */
    public function getMethodAnnotation(
        ReflectionMethod $method,
        $annotationName
    );

    /**
     * Retrieve a list of property level annotations.
     *
     * @param $property \ReflectionProperty Property to retrieve
     *        annotations from.
     */
    public function getPropertyAnnotations(ReflectionProperty $property);

    /**
     * Retrieve a property level annotation based on annotation name.
     *
     * @param $property \ReflectionProperty Property to retrieve
     *        annotations from.
     * @param $annotationName string The name of the annotation to retrieve.
     */
    public function getPropertyAnnotation(
        ReflectionProperty $property,
        $annotationName
    );
}
