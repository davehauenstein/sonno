<?php

/**
 * @category  Sonno
 * @package   Sonno\Http
 * @author    Dave Hauenstein <davehauenstein@gmail.com>
 * @author    Tharsan Bhuvanendran <me@tharsan.com>
 * @author    360i <sonno@360i.com>
 * @copyright Copyright (c) 2011 360i LLC (http://360i.com)
 * @license   http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Http;

/**
 * Abstraction for a resource representation variant. This is an immutable
 * class.
 *
 * @category Sonno
 * @package  Sonno\Http
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 */
class Variant
{
    /**
     * The content encoding of the variant - may be null.
     *
     * @var string
     */
    private $_encoding;

    /**
     * The language of the variant - may be null.
     *
     * @var string
     */
    private $_language;

    /**
     * The media type of the variant - may be null.
     *
     * @var string
     */
    private $_mediaType;

    /**
     * Create a new instance of Variant.
     *
     * @param null|string $encoding  The content encoding of the variant.
     * @param null|string $language  The language of the variant.
     * @param null|string $mediaType The media type of the variant.
     */
    public function __construct(
        $encoding  = null,
        $language  = null,
        $mediaType = null
    )
    {
        $this->_encoding  = $encoding;
        $this->_language  = $language;
        $this->_mediaType = $mediaType;
    }

    /**
     * Get the encoding of the variant.
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Get the language of the variant.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * Get the media type of the variant.
     *
     * @return string
     */
    public function getMediaType()
    {
        return $this->_mediaType;
    }

    /**
     * Calculate a unique identifier using the three properties of this variant.
     *
     * @return string
     */
    public function getFingerprint()
    {
        return sha1($this->_encoding . $this->_language . $this->_mediaType);
    }
}
