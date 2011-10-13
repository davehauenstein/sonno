<?php

/**
 * @category Sonno
 * @package  Test
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Annotation;

use Sonno\Annotation\Consumes;

/**
 * Class level documentation.
 *
 * @category Sonno
 * @package  Test
 */
class ConsumesTest extends \PHPUnit_Framework_TestCase
{
    public function testConsumesConstructorSettingAndGetters()
    {
        $mediaTypes = array('image/jpg', 'application/xml');
        $consumes = new Consumes($mediaTypes);
        $this->assertEquals($mediaTypes, $consumes->getMediaTypes());
    }
}
