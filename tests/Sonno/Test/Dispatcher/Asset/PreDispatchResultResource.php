<?php

/**
 * @category Sonno
 * @package  Sonno\Test\Dispatcher
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Dispatcher\Asset;

/**
 * Test the default Dispatcher class provided as part of Sonno.
 *
 * @category Sonno
 * @package  Test
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */
class PreDispatchResultResource
{
    public function testPreDispatch()
    {
    }

    public function preDispatch()
    {
        return new \Sonno\Http\Response\Response(200, 'Result from Pre-Dispatch');
    }

    public function postDispatch()
    {

    }
}
