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
 * Defines the media types that the methods of a resource class can accept.
 * If not specified, a container will assume that any media type is acceptable. 
 * Method level annotations override a class level annotation. A container is
 * responsible for ensuring that the method invoked is capable of consuming the
 * media type of the HTTP request entity body. If no such method is available
 * the container must respond with a HTTP "415 Unsupported Media Type" as
 * specified by RFC 2616.
 *
 * Method level Consumes annotation will override class level Consumes
 * annotation.
 *
 * @Annotation
 * @category Sonno
 * @package  Sonno\Annotation
 */
class Consumes
{
    /**
     * A list of media types. E.g. {"image/jpeg","image/gif"}.
     *
     * @var array
     */
    protected $_mediaTypes = array('*/*');

    /**
     * Construct a new Consumes instance.
     *
     * @param $mediaTypes array A list of media types.
     *        E.g. {"image/jpeg","image/gif"}.
     */
    public function __construct(array $mediaTypes = null)
    {
        if (isset($mediaTypes['value'])) {
            $mediaTypes = $mediaTypes['value'];
        }

        if (null !== $mediaTypes) {
            $this->_mediaTypes = $mediaTypes;
        }
    }

    /**
     * Getter for _mediaTypes property.
     *
     * @return string Value of _mediaTypes property.
     */
    public function getMediaTypes()
    {
        return $this->_mediaTypes;
    }
}
