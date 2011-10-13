<?php

/**
 * @category Sonno
 * @package  Test
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Annotation;

use Sonno\Annotation\Context;

/**
 * Class level documentation.
 *
 * @category Sonno
 * @package  Test
 */
class ContextTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSetter()
    {
        $context = new Context('Request');
        $this->assertEquals($context->getContext(), 'Request');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testContext()
    {
        $context = new Context('InvalidName');
    }
}
