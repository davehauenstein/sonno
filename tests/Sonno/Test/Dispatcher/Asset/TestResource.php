<?php

/**
 * @category Sonno
 * @package  Sonno\Test\Dispatcher
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Dispatcher\Asset;

use Sonno\Test\Dispatcher\Asset\Jacket;

/**
 * Test the default Dispatcher class provided as part of Sonno.
 *
 * @category Sonno
 * @package  Test
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */
class TestResource
{
    /**
     * @var \Sonno\Uri\UriInfo
     * @Context("UriInfo")
     */
    protected $_uriInfo;

    /**
     * @var \Sonno\Http\Request\RequestInterface
     * @Context("Request")
     */
    protected $_request;

    public function testUriInfoInjection()
    {
        return $this->_uriInfo->getAbsolutePath();
    }

    public function testRequestInjection()
    {
        return $this->_request->getRequestUri();
    }

    public function testPathArgument($user)
    {
        return $user;
    }

    public function testQueryArgument($user)
    {
        return $user;
    }

    public function testHeaderArgument($ApiUser)
    {
        return $ApiUser;
    }

    public function testFormArgument($user)
    {
        return $user;
    }

    public function testUnrenderedObjectArgument(Jacket $jacket)
    {
        return sprintf(
            "colour=%s&brand=%s",
            $jacket->getColour(),
            $jacket->getBrand()
        );
    }
}
