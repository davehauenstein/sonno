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

use Sonno\Http\Variant;

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
     * @param Variant $mediaType The media (MIME) type to produce a
     *                           representation in.
     *
     * @return mixed A scalar value.
     */
    public function render(Variant $mediaType);

    /**
     * Create a PHP object from a rendered representation.
     *
     * @param mixed   $representation The rendered representation.
     * @param Variant $mediaType      The media (MIME) type of the supplied
     *                                representation.
     *
     * @return object
     */
    public static function unrender($representation, Variant $mediaType);
}
