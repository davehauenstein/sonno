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
     * Return the content length of the body sent in the request.
     *
     * @return int
     */
    public function getContentLength();

    /**
     * Return the content type of the body sent in the request.
     *
     * @returns string
     */
    public function getContentType();

    /**
     * Return an array of headers.
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Return the raw, unmodified request body.
     *
     * A client may specify content in the request body. This method will
     * read in that content and return it completely unaltered or formatted.
     *
     * @return string
     */
    public function getRequestBody();

    /**
     * Return an array of query parameters.
     *
     * @return array
     */
    public function getQueryParams();

    /**
     * Return a specific query parameter.
     *
     * @return string
     */
    public function getQueryParam($param);

    /**
     * Return whether or not this request uses a secure connection (https).
     *
     * @return boolean
     */
    public function isSecure();

    /**
     * Return the port number used for the request.
     *
     * @return int
     */
    public function getPort();

    /**
     * Convenience method for determining if the request is a GET request.
     *
     * @return boolean
     */
    public function isGet();

    /**
     * Convenience method for determining if the request is a POST request.
     *
     * @return boolean
     */
    public function isPost();

    /**
     * Convenience method for determining if the request is a PUT request.
     *
     * @return boolean
     */
    public function isPut();

    /**
     * Convenience method for determining if the request is a DELETE request.
     *
     * @return boolean
     */
    public function isDelete();

    /**
     * Convenience method for determining if the request is a HEAD request.
     *
     * @return boolean
     */
    public function isHead();

    /**
     * Convenience method for determining if the request is a TRACE request.
     *
     * @return boolean
     */
    public function isTrace();

    /**
     * Convenience method for determining if the request is a OPTIONS request.
     *
     * @return boolean
     */
    public function isOptions();

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
