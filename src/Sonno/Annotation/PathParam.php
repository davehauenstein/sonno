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

use Sonno\Annotation\ParameterContainer;

/**
 * Binds the value of a URI template parameter or a path segment containing
 * the template parameter to a resource method parameter or resource class
 * field. The value is URL decoded.
 *
 * @Annotation
 * @category Sonno
 * @package  Sonno\Annotation
 */
class PathParam extends ParameterContainer
{

}
