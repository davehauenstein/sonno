<?php

/**
 * @category Sonno
 * @package  Test
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test;

use Sonno\Http\MediaType;

/**
 * Class level documentation.
 *
 * @category Sonno
 * @package  Test
 */
class MediaTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testTypeSubtypeParamsAreSettableViaConstructorAndGettable()
    {
        $type    = 'application';
        $subtype = 'xml';
        $params  = array('q' => 0.5, 'limit' => 1);

        $mediaType = new mediaType($type, $subtype, $params);
        $mtParams  = $mediaType->getParameters();
        $this->assertEquals($params['q'], $mtParams['q']);
        $this->assertEquals($params['limit'], $mtParams['limit']);
        $this->assertEquals($type, $mediaType->getType());
        $this->assertEquals($subtype, $mediaType->getSubtype());
    }

    public function testSubtypeIsAWildCard()
    {
        $type      = 'application';
        $subtype   = '*';
        $mediaType = new mediaType($type, $subtype);
        $this->assertTrue($mediaType->isWildcardSubtype());

        $type      = 'application';
        $subtype   = 'xml';
        $mediaType = new mediaType($type, $subtype);
        $this->assertFalse($mediaType->isWildcardSubtype());
    }
    
    public function testTypeIsAWildCard()
    {
        $type      = '*';
        $subtype   = '*';
        $mediaType = new mediaType($type, $subtype);
        $this->assertTrue($mediaType->isWildcardType());

        $type      = 'application';
        $subtype   = 'xml';
        $mediaType = new mediaType($type, $subtype);
        $this->assertFalse($mediaType->isWildcardType());
    }
}
