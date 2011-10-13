<?php

/**
 * @category Sonno
 * @package  Sonno\Http
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 * @author     360i <sonno@360i.com>
 * @copyright  Copyright (c) 2011 360i LLC (http://360i.com)
 * @license    http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Http;

/**
 * An abstraction for a media type. Instances are immutable.
 *
 * @category Sonno
 * @package  Sonno\Http
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @todo     Implement isCompatible method.
 */
class MediaType
{
    /**
     * "application/atom+xml"
     */
    const APPLICATION_ATOM_XML = 'application/atom+xml';

    /**
     * "application/x-www-form-urlencoded"
     */
    const APPLICATION_FORM_URLENCODED = 'application/x-www-form-urlencoded';

    /**
     * "application/json"
     */
    const APPLICATION_JSON = 'application/json';

    /**
     * "application/octet-stream"
     */
    const APPLICATION_OCTET_STREAM = 'application/octet-stream';

    /**
     * "application/svg+xml"
     */
    const APPLICATION_SVG_XML = 'application/svg+xml';

    /**
     * "application/xhtml+xml"
     */
    const APPLICATION_XHTML_XML = 'application/xhtml+xml';

    /**
     * "application/xml"
     */
    const APPLICATION_XML = 'application/xml';

    /**
     * "*"
     */
    const MEDIA_TYPE_WILDCARD = '*';

    /**
     * "multipart/form-data"
     */
    const MULTIPART_FORM_DATA = 'multipart/form-data';

    /**
     * "text/html"
     */
    const TEXT_HTML = 'text/html';

    /**
     * "text/plain"
     */
    const TEXT_PLAIN = 'text/plain';

    /**
     * "text/xml"
     */
    const TEXT_XML = 'text/xml';

    /**
     * "*slash*"
     */
    const WILDCARD = '*/*';

    /**
     * Type of media-type, "application" for media-type "application/json".
     *
     * @var string
     */
    protected $_type;

    /**
     * Subtype of media-type, "json" for media-type "application/json".
     *
     * @var string
     */
    protected $_subtype;

    /**
     * A list of parameters associated with a media type.
     *
     * @var array
     */
    protected $_parameters;

    /**
     * Construct a new media type.
     *
     * @param string $type 
     * @param string $subtype 
     * @param string $parameters 
     */
    public function __construct(
        $type = null,
        $subtype = null,
        $parameters = array()
    )
    {
        $this->_type = (null === $type || empty($type))
            ? self::MEDIA_TYPE_WILDCARD
            : $type;
        $this->_subtype = (null === $subtype || empty($subtype))
            ? self::MEDIA_TYPE_WILDCARD
            : $subtype;
        $this->_parameters = (array) $parameters;
    }

    /**
     * Getter for read-only parameter list.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * Getter for media-type subtype. For example, the media-type,
     * application/json, "json" would be returned.
     *
     * @return string
     */
    public function getSubtype()
    {
        return $this->_subtype;
    }

    /**
     * Getter for media-type type. For example, the media-type,
     * application/json, "application" would be returned.
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Check if this media type is compatible with another media type.
     *
     * @param Sonno\Http\MediaType $other 
     * @return boolean
     * @todo   Implement this method.
     */
    public function isCompatible($other)
    {
        
    }

    /**
     * Whether or not the subtype of the media-type is a wildcard.
     *
     * Usage:
     * <pre>
     *     use Sonno\Http\MediaType;
     *
     *     // Will return false
     *     $mediaType = new MediaType('application', 'xml');
     *     $isWildcard = $mediaType->isWildcardSubtype();
     *
     *     // Will return true
     *     $mediaType = new MediaType('application', '*');
     *     $isWildcard = $mediaType->isWildcardSubtype();
     * </pre>
     *
     * @return boolean
     */
    public function isWildcardSubtype()
    {
        return (self::MEDIA_TYPE_WILDCARD == $this->_subtype);
    }

    /**
     * Whether or not the type of the media-type is a wildcard.
     *
     * Usage:
     * <pre>
     *     use Sonno\Http\MediaType;
     *
     *     // Will return false
     *     $mediaType = new MediaType('application', 'xml');
     *     $isWildcard = $mediaType->isWildcardType();
     *
     *     // Will return true
     *     $mediaType = new MediaType('*', '*');
     *     $isWildcard = $mediaType->isWildcardType();
     * </pre>
     *
     * @return boolean
     */
    public function isWildcardType()
    {
        return (self::MEDIA_TYPE_WILDCARD == $this->_type);
    }
}