<?php

/**
 * @category   Sonno
 * @package    Sonno\Http\Response
 * @subpackage Response
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 * @author     Tharsan Bhuvanendran <me@tharsan.com>
 * @author     360i <sonno@360i.com>
 * @copyright  Copyright (c) 2011 360i LLC (http://360i.com)
 * @license    http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Http\Response;

use Sonno\Http\Response\ResponseException;

/**
 * A class to model an HTTP response.
 *
 * @category   Sonno
 * @package    Sonno\Http\Response
 * @subpackage Response
 * @author     Dave Hauenstein <davehauenstein@gmail.com>
 */
class Response
{
    /**
     * A list of valid HTTP Status Code.
     *
     * @var array
     */
    static public $statusCodes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    );

    /**
     * Output stream to write content to.
     *
     * @var string
     */
    protected static $_contentOutputStream = 'php://output';

    /**
     * Response body content.
     *
     * @var string
     */
    protected $_content;

    /**
     * Respose status code.
     *
     * @var int
     */
    protected $_statusCode;

    /**
     * An array of response headers.
     *
     * @var array
     */
    protected $_headers = array();

    /**
     * HTTP Protocol Version.
     *
     * @var string
     */
    protected $_protocolVersion = '1.1';

    /**
     * Construct a new HTTP Response.
     *
     * @param  string $code Response Status Code.
     * @param  string $content Response body content.
     * @param  string $headers Response headers.
     * @return void
     */
    public function __construct($code = 200, $content = '', $headers = array())
    {
        $this->setContent($content)
             ->setStatusCode($code)
             ->setHeaders((array) $headers);
    }

    /**
     * Set HTTP response content body.
     *
     * @param string $content Response body content.
     * @return Sonno\Http\Response Implements fluent interface.
     */
    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    /**
     * Set HTTP response status code.
     *
     * @param string $statusCode 
     * @return Sonno\Http\Response Implements fluent interface.
     */
    public function setStatusCode($statusCode)
    {
        $this->_statusCode = (int) $statusCode;
        return $this;
    }

    /**
     * Set HTTP response headers.
     *
     * @param array $headers An array of response headers.
     * @return Sonno\Http\Response Implements fluent interface.
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $header => $value) {
            $this->_headers[$header] = $value;
        }
        return $this;
    }

    /**
     * Returns true/false whether or not a header has been set.
     *
     * @param string $header 
     * @return boolean Whether or not a header is set.
     */
    public function hasHeader($header)
    {
        return (isset($this->_headers[$header]));
    }

    /**
     * Returns a header
     *
     * @param string $header 
     * @return boolean Whether or not a header is set.
     */
    public function getHeader($header)
    {
        return (isset($this->_headers[$header]))
            ? $this->_headers[$header]
            : null;
    }

    /**
     * Unset a header from the headers array.
     *
     * @param string $header 
     * @return Sonno\Http\Response Implements fluent interface.
     */
    public function removeHeader($header)
    {
        if ($this->hasHeader($header)) {
            unset($this->_headers[$header]);
        }
        return $this;
    }

    /**
     * Returns an array of headers.
     *
     * @param array $headers 
     * @return array Return an array of headers.
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Get HTTP response content body.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Returns the HTTP Response status code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    /**
     * A convenience method for appropriately setting headers and the response
     * code for an HTTP "Created" response.
     *
     * @param string $location 
     * @return Sonno\Http\Response Implements fluent interface.
     */
    public function setCreated($location)
    {
        $this->setStatusCode(201);
        $this->setHeaders(array('Location' => $location));
        return $this;
    }

    /**
     * A convenience method for appropriately setting headers and the response
     * code for an HTTP "Not Modified" response.
     *
     * @return Sonno\Http\Response Implements fluent interface.
     */
    public function setNotModified()
    {
        $this->setStatusCode(304);
        $this->setContent('');
        $entityHeaders = array(
            'Allow',
            'Content-Encoding',
            'Content-Language',
            'Content-Length',
            'Content-Location',
            'Content-MD5',
            'Content-Range',
            'Content-Type',
            'Expires',
            'Last-Modified',
        );
        foreach ($entityHeaders as $header) {
            $this->removeHeader($header);
        }
        return $this;
    }

    /**
     * Send both the HTTP headers as well as the content body.
     *
     * @param  $sendHeaders Whether or not to send headers.
     * @return Sonno\Http\Response Implements fluent interface.
     * @throws Sonno\Http\Response\ResponseException If an error occured while
     *         writing content to the response body.
     */
    public function sendResponse($sendHeaders = true)
    {
        if ($sendHeaders) {
            $this->sendHeaders();
        }

        $result = file_put_contents(
            static::$_contentOutputStream,
            $this->_content
        );

        if (false === $result) {
            throw new ResponseException(
                'There was an error while writing content to the output stream'
            );
        }
    }

    /**
     * Send HTTP headers.
     *
     * @return Sonno\Http\Response Implements fluent interface.
     * @todo   Send cookies.
     */
    public function sendHeaders()
    {
        foreach ($this->_headers as $header => $value) {
            header($header . ': ' . $value);
        }

        header(
            sprintf(
                'HTTP/%s %s %s',
                $this->_protocolVersion,
                $this->_statusCode,
                self::$statusCodes[$this->_statusCode]
            )
        );
    }

    /**
     * Setter for static property of the content output stream.
     *
     * @param  $contentOutputStream A writable stream wrapper.
     * @return void
     */
    public static function setContentOutputStream($contentOutputStream)
    {
        static::$_contentOutputStream = $contentOutputStream;
    }
}
