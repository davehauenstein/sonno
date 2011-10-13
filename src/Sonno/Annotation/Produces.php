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
 * Defines the media type(s) that the methods of a resource class can produce.
 * If not specified then a container will assume that any type can be produced.
 * Method level annotations override a class level annotation. A container is
 * responsible for ensuring that the method invoked is capable of producing
 * one of the media types requested in the HTTP request. If no such method is
 * available the container must respond with a HTTP "406 Not Acceptable" as
 * specified by RFC 2616.
 *
 * A method for which there is a single-valued Produces is not required to set
 * the media type of representations that it produces: the container will
 * use the value of the Produces when sending a response.
 *
 * Method level Produces annotation will override class level Produces
 * annotation.
 *
 * @Annotation
 * @category Sonno
 * @package  Sonno\Annotation
 */
class Produces
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
