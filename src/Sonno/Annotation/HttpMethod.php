<?php

/**
 * @category Sonno
 * @package  Sonno\Annotation
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 * @author     360i <sonno@360i.com>
 * @copyright  Copyright (c) 2011 360i LLC (http://360i.com)
 * @license    http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Annotation;

/**
 * Associates the name of a HTTP method with an annotation. It is an error
 * for a method to be annotated with more than one annotation that is
 * annotated with HttpMethod. 
 *
 * @Annotation
 * @category Sonno
 * @package  Sonno\Annotation
 */
abstract class HttpMethod
{
    abstract public function __toString();
}
