<?php

/**
 * @category   Sonno
 * @package    Sonno\Http\Request
 * @subpackage Request
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 * @author     Tharsan Bhuvanendran <me@tharsan.com>
 * @author     360i <sonno@360i.com>
 * @copyright  Copyright (c) 2011 360i LLC (http://360i.com)
 * @license    http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Http\Request;

use Sonno\Http\Request\RequestInterface,
    DateTime,
    InvalidArgumentException;

/**
 * A class to model an HTTP request.
 *
 * An instance of the request can be built in two ways:
 *   1. constructor injection of a server array.
 *   2. a static factory method that uses the parameters of current request.
 *
 * Method 1 usage:
 * <pre>
 *   use Sonno\Http\Request\Request;
 *   $request = new Request($requestData);
 * </pre>
 *
 * The $requestData will be parsed and distributed to the corresponding
 * properties of the class. The $requestData structure should mimic PHP's
 * {@link http://www.php.net/manual/en/reserved.variables.server.php $_SERVER}
 * variable.
 *
 * Method 2 usage:
 * <pre>
 *   use Sonno\Http\Request\Request;
 *   $request = Request::getInstanceOfCurrentRequest();
 * </pre>
 *
 * Method 2 will use PHP's super global
 * {@link http://www.php.net/manual/en/reserved.variables.server.php $_SERVER}
 * to build a Request object.
 *
 * @category   Sonno
 * @package    Sonno\Http\Request
 * @subpackage Request
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 * @todo       Implement both argument version of evaluatePreconditions method.
 */
class Request implements RequestInterface
{
    /**
     * Constant defining the string for the HTTP verb 'GET'.
     */
    const HTTP_VERB_GET     = 'GET';

    /**
     * Constant defining the string for the HTTP verb 'POST'.
     */
    const HTTP_VERB_POST    = 'POST';

    /**
     * Constant defining the string for the HTTP verb 'PUT'.
     */
    const HTTP_VERB_PUT     = 'PUT';

    /**
     * Constant defining the string for the HTTP verb 'DELETE'.
     */
    const HTTP_VERB_DELETE  = 'DELETE';

    /**
     * Constant defining the string for the HTTP verb 'HEAD'.
     */
    const HTTP_VERB_HEAD    = 'HEAD';

    /**
     * Constant defining the string for the HTTP verb 'TRACE'.
     */
    const HTTP_VERB_TRACE   = 'TRACE';

    /**
     * Constant defining the string for the HTTP verb 'OPTIONS'.
     */
    const HTTP_VERB_OPTIONS = 'OPTIONS';

    /**
     * The PHP input stream to retrieve the HTTP request body from.
     *
     * See more about php's
     * {@link http://php.net/manual/en/wrappers.php.php stream wrappers}
     *
     * @var string
     */
    public static $bodyStreamWrapper = 'php://input';

    /**
     * A key/value pairing of http request headers to their values.
     * All keys will be lower-case.
     *
     * @var array<string>
     */
    protected $_headers = array();

    /**
     * The incoming request URI w/out the host. For example, the requestUri
     * value for the follow URI, http://www.example.com/api/v1/users/5, will
     * be /api/v1/users/5. A forward slash will always start the sting,
     * however, the trailing forward slash will always be omitted.
     *
     * @var string
     */
    protected $_requestUri;

    /**
     * The HTTP verb for which the resource is being requested with.
     * The value will be one of the following: GET, POST, PUT, DELETE,
     * HEAD, TRACE, OPTIONS.
     *
     * @var string
     */
    protected $_requestMethod;

    /**
     * Raw, unaltered request body.
     *
     * @var string
     */
    protected $_requestBody;

    /**
     * In bytes, the size of the body of the request.
     *
     * @var string
     */
    protected $_contentLength = 0;

    /**
     * The media type of the body of the request sent by the client.
     *
     * @var string
     */
    protected $_contentType;

    /**
     * An array of query string parameters retrieved from the URI.
     *
     * @var array
     */
    protected $_queryParams;

    /**
     * A FQNS and class name that represents the default class to use when
     * generating response objects.
     *
     * @var string
     */
    protected static $_defaultResponseClass = 'Sonno\Http\Response\Response';

    /**
     * Construct a new instance of a requst. A constructor takes a argument of
     * $requestData, for more information on setting it up, see:
     * {@link http://php.net/manual/en/reserved.variables.server.php}.
     *
     * @param array $requestData
     */
    public function __construct(array $requestData = array())
    {
        $this->_contentType = isset($requestData['CONTENT_TYPE'])
            ? $requestData['CONTENT_TYPE']
            : null;
        $this->_contentLength = isset($requestData['CONTENT_LENGTH'])
            ? $requestData['CONTENT_LENGTH']
            : null;
        $this->_requestMethod = isset($requestData['REQUEST_METHOD'])
            ? $requestData['REQUEST_METHOD']
            : null;
        if (isset($requestData['QUERY_STRING'])) {
            parse_str($requestData['QUERY_STRING'], $this->_queryParams);
        }
        if (isset($requestData['REQUEST_URI'])) {
            $requestUri = $requestData['REQUEST_URI'];
            $pos = strpos($requestUri, '?');
            if ($pos) {
                $requestUri = substr($requestUri, 0, $pos);
            }
            $this->_requestUri = '/' . trim($requestUri, '/');
        }

        // set headers
        foreach ($requestData as $key => $val) {
            if ('HTTP_' == substr($key, 0, 5)) {
                $name = str_replace('_', '-', strtolower(substr($key, 5)));
                $this->_headers[$name] = $val;
            }
        }
    }

    /**
     * Return the HTTP verb used for this request.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->_requestMethod;
    }

    /**
     * Return the incoming request URI w/out the host.
     * See {@link Sonno\Http\Request\Request#_requestUri}.
     *
     * @return string
     */
    public function getRequestUri()
    {
        return $this->_requestUri;
    }

    /**
     * Return the content length of the body sent in the request.
     *
     * @return int
     */
    public function getContentLength()
    {
        return $this->_contentLength;
    }

    /**
     * Return the content type of the body sent in the request.
     *
     * @returns string
     */
    public function getContentType()
    {
        return $this->_contentType;
    }

    /**
     * Return an array of headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Return the raw, unmodified request body.
     *
     * A client may specify content in the request body. This method will
     * read in that content and return it completely unaltered or formatted.
     *
     * @return string
     */
    public function getRequestBody()
    {
        if (null === $this->_requestBody) {
            $handle = fopen(self::$bodyStreamWrapper, 'r');
            while ($chunk = fread($handle, 8192)) {
               $this->_requestBody .= $chunk;
            }
        }
        return $this->_requestBody;
    }

    /**
     * Return an array of query parameters.
     *
     * @return array
     */
    public function getQueryParams()
    {
        return $this->_queryParams;
    }

    /**
     * Return a specific query parameter.
     *
     * @return string
     */
    public function getQueryParam($param)
    {
        return isset($this->_queryParams[$param])
            ? $this->_queryParams[$param]
            : null;
    }

    /**
     * Convenience method for determining if the request is a GET request.
     *
     * @return boolean
     */
    public function isGet()
    {
        return (self::HTTP_VERB_GET == $this->_requestMethod);
    }

    /**
     * Convenience method for determining if the request is a POST request.
     *
     * @return boolean
     */
    public function isPost()
    {
        return (self::HTTP_VERB_POST == $this->_requestMethod);
    }

    /**
     * Convenience method for determining if the request is a PUT request.
     *
     * @return boolean
     */
    public function isPut()
    {
        return (self::HTTP_VERB_PUT == $this->_requestMethod);
    }

    /**
     * Convenience method for determining if the request is a DELETE request.
     *
     * @return boolean
     */
    public function isDelete()
    {
        return (self::HTTP_VERB_DELETE == $this->_requestMethod);
    }

    /**
     * Convenience method for determining if the request is a HEAD request.
     *
     * @return boolean
     */
    public function isHead()
    {
        return (self::HTTP_VERB_HEAD == $this->_requestMethod);
    }

    /**
     * Convenience method for determining if the request is a TRACE request.
     *
     * @return boolean
     */
    public function isTrace()
    {
        return (self::HTTP_VERB_TRACE == $this->_requestMethod);
    }

    /**
     * Convenience method for determining if the request is a OPTIONS request.
     *
     * @return boolean
     */
    public function isOptions()
    {
        return (self::HTTP_VERB_OPTIONS == $this->_requestMethod);
    }

    /**
     * Select the representation variant that best matches the request.
     * More explicit variants are chosen ahead of less explicit ones.
     *
     * Steps for content negotiation:
     * 
     * <li>
     * 1. If no accept header is present, assume the client will accept
     *    anything. See section 10.4.7 of the rfc2616,
     *    {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html}.
     * </li><li>
     * 2. Use process of elimination to compare acceptable media types
     *    with the types specified by the variants. Keep track of potential
     *    variants along with their quality score.
     * </li><li>
     * 3. Once a list of potential variants has been formed, sort by the
     *    quality score and choose the variant with the highest score.
     *    If there is a tie, the one chosen is arbitrary.
     * </li><li>
     * 4. If no variants can be found null is returned. This
     *    should result in an HTTP 406 Not Acceptable response.
     * </li>
     *
     * @param  $variants<Sonno\Http\Variant> An array of variants.
     * @return null|Sonno\Http\Variant Will return null if a variant couldn't
     *         be selected.
     * @todo   Add support for variants with wildcards and a test case for it.
     * @todo   Add accept-encoding to content negotiation.
     * @todo   Add accept-language to content negotiation.
     */
    public function selectVariant(array $variants)
    {
        $headers = $this->getHeaders();
        if (!isset($headers['accept'])) {
            $acceptTypes = array(array(
                'quality' => 1,
                'type'    => '*',
                'subtype' => '*',
            ));
        } else {
            $acceptTypes = static::parseAcceptHeader($headers['accept']);
        }

        $acceptable = array();
        foreach ($acceptTypes as $acceptType) {
            $mediaType = $acceptType['type'] . '/' . $acceptType['subtype'];
            $acceptable[$mediaType] = $acceptType['quality'];
        }

        $potential = array();
        foreach ($variants as $variant) {
            $mediaType = $variant->getMediaType();
            list($type, $subtype) = preg_split('/\//', $mediaType);

            $found = null;
            if (isset($acceptable[$variant->getMediaType()])) {
                $found = $variant->getMediaType();
            } else if (isset($acceptable[$type . '/*'])) {
                $found = $type . '/*';
            } else if (isset($acceptable['*/*'])) {
                $found = '*/*';
            }

            if (null !== $found) {
                if ($acceptable[$found] == 0) {
                    continue;
                }
                $score = $acceptable[$found]*1000;
                // Specificity gets a bonus.
                if (false !== strpos($subtype, '+')) {
                    $score += 1;
                }
                if (isset($potential[$score])) {
                    $potential[$score] = array($potential[$score], $variant);
                } else {
                    $potential[$score] = $variant;
                }
            }
        }

        if (count($potential) == 0) {
            return null;
        }

        ksort($potential, SORT_NUMERIC);
        $selected = array_pop($potential);
        return (is_array($selected)) ? $selected[0] : $selected;
    }

    /**
     * Evaluate request preconditions based on the passed in values.
     *
     * If the precondition is NOT met, the server should return a 304 response,
     * specifying that the state of the client is in sync with the state of the
     * application resource.
     *
     * Note: If the request normally (i.e., without a conditional header) would
     * result in anything other than a 2xx or 412 status, the
     * conditional header SHOULD be ignored.
     *
     * Note: The HTTP specification does not define the actions a server should
     * take if both If-Modified-Since and If-Unmodified-Since OR both
     * If-Match and If-None-Match headers appear in the same request. This
     * method gives precedence to If-Modified-Since and If-Match headers. If
     * either of these are specified as well as their counter part, their
     * counter part will be ignored. See:
     * {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.25}
     * {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.26}
     *
     * NOTE: The If-Range request header is not currently supported.
     *
     * @param string $lastModified 
     * @param string $eTag 
     * @return Sonno\Http\Response null if the preconditions are met or a
     *         Response set with the appropriate status if the
     *         preconditions are not met. A returned Response will
     *         include an ETag header set with the value of eTag.
     * @throws \InvalidArgumentException If both parameters are null.
     */
    public function evaluatePreconditions($lastModified = null, $eTag = null)
    {
        if (null !== $lastModified && null !== $eTag) {
            throw new InvalidArgumentException(
                'Method currently only supports supplying either ' .
                '$lastModified or $eTag, not both.'
            );
        } else if (null !== $lastModified) {
            if (!($lastModified = date_create($lastModified))) {
                throw new InvalidArgumentException(
                    'Invalid $lastModified argument'
                );
            }

            $result = $this->_evaluateModifiedSince($lastModified);
            if (true === $result) {
                return null;
            } else if (false === $result) {
                $response = new self::$_defaultResponseClass();
                return $response->setNotModified();
            }

            $result = $this->_evaluateUnModifiedSince($lastModified);
            if (true === $result) {
                return null;
            } else if (false === $result) {
                $response = new self::$_defaultResponseClass(412);
                return $response;
            }
        } else if (null !== $eTag) {
            $result = $this->_evaluateEntityTagNoneMatch($eTag);
            if (true === $result) {
                return null;
            } else if (false === $result) {
                $response = new self::$_defaultResponseClass();
                return $response->setNotModified();
            }

            $result = $this->_evaluateEntityTagMatch($eTag);
            if (true === $result) {
                return null;
            } else if (false === $result) {
                $response = new self::$_defaultResponseClass(412);
                return $response;
            }
        } else {
            throw new InvalidArgumentException(
                'Neither $lastModifed nor $eTag were specified as arguments.'
            );
        }
    }

    /**
     * Returns an instance of the Sonno\Http\Request\Request object using the
     * $_SERVER variable to populate all properties.
     *
     * usage:
     *
     * <pre>
     *   use Sonno\Http\Request\Request;
     *   $request = Request::getInstanceOfCurrentRequest();
     *   $headers = $request->getHeaders();
     *   $method  = $request->getMethod();
     * </pre>
     *
     * @return void
     */
    public static function getInstanceOfCurrentRequest()
    {
        return new static($_SERVER);
    }

    /**
     * This method will parse accept* headers and itemize each component into
     * an array. An example of a datastructure that will be returned by this
     * method:
     *
     * <pre>
     * Array (
     *     [0] => Array
     *         (
     *             [quality] => 0.9
     *             [type] => text
     *             [subtype] => html
     *         )
     *     [1] => Array
     *         (
     *             [quality] => 0.8
     *             [type] => text
     *             [subtype] => plain
     *         )
     * )
     * </pre>
     *
     * @param  string $header 
     * @return array
     * @todo   Modify to handle accept-encoding, language, charset.
     */
    public static function parseAcceptHeader($header)
    {
        $acceptTypes    = array();
        $acceptTypesRaw = preg_split('/\s*,\s*/', $header);
        $qualityRegEx   = '/q\s*=\s*(0\.[0-9]+|1|0)/';
        foreach ($acceptTypesRaw as $type) {
            $acceptType = array('quality' => (float) 1);
            $parts      = preg_split('/\s*;\s*/', $type);
            list($type, $subtype)  = preg_split('/\//', $parts[0]);
            $acceptType['type']    = $type;
            $acceptType['subtype'] = $subtype;
            if (isset($parts[1]) &&
               false !== preg_match($qualityRegEx, $parts[1], $matches)
            ) {
                if (isset($matches[1])) {
                    $acceptType['quality'] = (float) $matches[1];
                }
            }
            array_push($acceptTypes, $acceptType);
        }
        return $acceptTypes;
    }

    /**
     * By default, the getRequestBody method will check php://input to retrieve
     * the raw body on a Request which specifies one.
     *
     * @param string $wrapper 
     * @return void
     */
    public static function setRequestBodyStreamWrapper($wrapper)
    {
        self::$bodyStreamWrapper = $wrapper;
    }

    /**
     * The precondition will be met if the If-Modified-Since header specifies
     * a time previous to the server's last-modified-time of the resourse
     * state.
     *
     * NOTE: If the If-Modified-Since header specifies an invalid date,
     * ignore header. See
     * {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.26}
     *
     * NOTE: If the If-Modified-Since header was not specified in the request,
     * NULL is returned.
     *
     * Examples:
     *
     * Request Header - If-Modified-Since: Sat, 29 Jan 2011 19:43:31 GMT
     * Server Last-Modified Time - Fri, 4 Feb 2011 14:21:19 GMT
     * Return TRUE - Precondition Met
     *
     * Request Header - If-Modified-Since: Sat, 29 Jan 2011 19:43:31 GMT
     * Server Last-Modified Time - Sat, 29 Jan 2011 19:43:31 GMT
     * Return FALSE - Precondition NOT Met
     *
     * @param int $serverTime Last-modified time of application resource state.
     * @return boolean Will return true if precondition is met, false if not.
     *         null if the if-modified-since header is not present or invalid.
     */
    protected function _evaluateModifiedSince(DateTime $serverTime)
    {
        if (!isset($this->_headers['if-modified-since']) ||
           !($modTime = date_create($this->_headers['if-modified-since']))
        ) {
            return null;
        }
        return $modTime < $serverTime;
    }

    /**
     * The precondition will be met, and therefore this function will return
     * true, if the If-Unmodified-Since header specifies a time later than the
     * server's last modified time of the resourse state.
     *
     * If the precondition is not met, the server MUST respond with a 412
     * (Precondition Failed) status. See
     * {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.28}
     *
     * NOTE: If the If-Unmodified-Since header specifies an invalid date,
     * ignore header. See
     * {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.28}
     *
     * NOTE: If the If-Unmodified-Since header was not specified in the
     * request, NULL is returned.
     *
     * Examples:
     *
     * Request Header - If-Unmodified-Since: Sat, 29 Jan 2011 19:43:31 GMT
     * Server Last-Modified Time - Fri, 4 Feb 2011 14:21:19 GMT
     * Return FALSE - Precondition Not Met
     *
     * Request Header - If-Unmodified-Since: Sun, 30 Jan 2011 16:14:28 GMT
     * Server Last-Modified Time - Sat, 29 Jan 2011 19:43:31 GMT
     * Return TRUE - Precondition Met
     *
     * @param int $serverTime Last-modified time of application resource state.
     * @return boolean Will return true if precondition is met, false if not.
     *         null if the if-modified-since header is not present or invalid.
     */
    protected function _evaluateUnModifiedSince(DateTime $serverTime)
    {
        if (!isset($this->_headers['if-unmodified-since']) ||
           !($modTime = date_create($this->_headers['if-unmodified-since']))
        ) {
            return null;
        }
        return $modTime > $serverTime;
    }

    /**
     * Evaluate the if-match conditional header.
     *
     * Note: This method ignores strong vs. weak E-Tag matching.
     *
     * @param string $serverTag 
     * @return boolean
     */
    protected function _evaluateEntityTagMatch($serverTag)
    {
        if (!isset($this->_headers['if-match'])) {
            return null;
        }

        $eTags = preg_split('/\s*,\s*/', $this->_headers['if-match']);
        $eTags = array_flip(
            array_map(
                function($val) {
                    return str_replace(array('/W', '"'), '', $val);
                },
                $eTags
            )
        );
        return isset($eTags['*']) || isset($eTags[$serverTag]);
    }

    /**
     * Evaluate the if-none-match conditional header.
     *
     * Note: This method ignores strong vs. weak E-Tag matching.
     *
     * @param string $serverTag 
     * @return boolean
     */
    protected function _evaluateEntityTagNoneMatch($serverTag)
    {
        if (!isset($this->_headers['if-none-match'])) {
            return null;
        }

        $eTags = preg_split('/\s*,\s*/', $this->_headers['if-none-match']);
        $eTags = array_flip(
            array_map(
                function($val) {
                    return str_replace(array('/W', '"'), '', $val);
                },
                $eTags
            )
        );
        return !isset($eTags['*']) && !isset($eTags[$serverTag]);
    }
}
