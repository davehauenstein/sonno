<?php

/**
 * @category Sonno
 * @package  Sonno\Http\Request
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 * @author     360i <sonno@360i.com>
 * @copyright  Copyright (c) 2011 360i LLC (http://360i.com)
 * @license    http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Http\Request;

/**
 * A class to model an HTTP request.
 *
 * @category Sonno
 * @package  Sonno\Http\Request
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 */
interface RequestInterface
{
    /**
     * Return the HTTP verb used for this request.
     *
     * @return string
     */
    public function getMethod();

    /**
     * Return the incoming request URI w/out the host.
     * See {@link Sonno\Http\Request\Request#_requestUri}.
     *
     * @return string
     */
    public function getRequestUri();

    /**
     * Select the representation variant that best matches the request.
     * More explicit variants are chosen ahead of less explicit ones.
     *
     * @return Sonno\Http\Variant
     * @throws Sonno\Http\Request\VariantException No variants can be selected.
     */
    public function selectVariant(array $variants);

    /**
     * Evaluate request preconditions based on the passed in value.
     *
     * @throws \InvalidArgumentException If both parameters are null.
     */
    public function evaluatePreconditions($lastModified = null, $eTag = null);
}
