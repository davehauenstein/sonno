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
 * Binds the value(s) of a HTTP query parameter to a resource method parameter
 * or resource class field. Values are URL decoded. A default value can be
 * specified using the DefaultValue annotation.
 *
 * @Annotation
 * @category Sonno
 * @package  Sonno\Annotation
 */
class QueryParam extends ParameterContainer
{

}
