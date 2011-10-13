<?php

/**
 * @category Sonno
 * @package  Test
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Response;

use Sonno\Http\Response\Response;

/**
 * Class level documentation.
 *
 * @category Sonno
 * @package  Test
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testHeaderSettersAndGetters()
    {
        $response = new Response();
        $headers  = array(
            'Content-Type' => 'application/xml',
            'Location'     => 'http://sonno.360i.com',
        );
        $response->setHeaders($headers);
        $this->assertEquals(
            $response->getHeaders(),
            $headers
        );
        $this->assertEquals(
            $response->getHeader('Content-Type'),
            $headers['Content-Type']
        );
        $this->assertEquals(
            $response->getHeader('Location'),
            $headers['Location']
        );
    }

    public function testHeaderRemoval()
    {
        $response = new Response();
        $headers  = array(
            'Content-Type' => 'application/xml',
            'Location'     => 'http://sonno.360i.com',
        );
        $response->setHeaders($headers);
        $response->removeHeader('Content-Type');
        $this->assertNull($response->getHeader('Content-Type'));
    }

    public function testHasHeader()
    {
        $response = new Response();
        $headers  = array(
            'Content-Type' => 'application/xml',
            'Location'     => 'http://sonno.360i.com',
        );
        $response->setHeaders($headers);
        $this->assertTrue($response->hasHeader('Content-Type'));
        $this->assertFalse($response->hasHeader('Non-Existent-Header'));
    }

    public function testContentSettersAndGetters()
    {
        $response = new Response();
        $content  = 'Some test content';
        $response->setContent($content);
        $this->assertEquals($response->getContent(), $content);
    }

    public function testSetCreated()
    {
        $response = new Response();
        $location = 'http://sonno.360i.com';
        $response->setCreated($location);
        $this->assertEquals($response->getStatusCode(), 201);
        $this->assertEquals($response->getHeader('Location'), $location);
    }

    public function testSetNotModified()
    {
        $response = new Response();
        $response->setHeaders(array(
            'Allow'            => 'test-value-to-be-removed',
            'Content-Encoding' => 'test-value-to-be-removed',
            'Content-Language' => 'test-value-to-be-removed',
            'X-Some-Header'    => 'test-value-to-NOT-be-removed',
        ));
        $response->setNotModified();
        $this->assertEquals($response->getStatusCode(), 304);
        $this->assertNull($response->getHeader('Allow'));
        $this->assertNull($response->getHeader('Content-Encoding'));
        $this->assertNull($response->getHeader('Content-Language'));
        $this->assertNotNull($response->getHeader('X-Some-Header'));
    }
}
