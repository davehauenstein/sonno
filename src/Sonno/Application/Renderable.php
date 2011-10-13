<?php

/**
 * @category Sonno
 * @package  Sonno\Application
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 * @author     360i <sonno@360i.com>
 * @copyright  Copyright (c) 2011 360i LLC (http://360i.com)
 * @license    http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Application;

/**
 * An interface to a class that produce a representation (render) itself.
 *
 * @category Sonno
 * @package  Sonno\Application
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */
interface Renderable
{
    /**
     * Create a representation of this object in the given media type.
     *
     * @param $mediaType The media (MIME) type to produce a representation in.
     * @return mixed A scalar value.
     */
    public function render($mediaType);
}
