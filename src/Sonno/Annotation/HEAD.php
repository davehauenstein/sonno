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

use Sonno\Annotation\HttpMethod;

/**
 * Indicates that the annotated method responds to HTTP HEAD requests.
 *
 * @Annotation
 * @category Sonno
 * @package  Sonno\Annotation
 */
class HEAD extends HttpMethod
{
    /**
     * Represent class as a string.
     *
     * @return string 'HEAD'
     */
    public function __toString()
    {
        return 'HEAD';
    }
}
