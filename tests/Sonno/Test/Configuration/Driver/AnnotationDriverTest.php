<?php

/**
 * @category Sonno
 * @package  Test
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Configuration\Driver;

require_once __DIR__ . '/Asset/ValidResourceBasic.php';

use Sonno\Configuration\Driver\AnnotationDriver,
    Sonno\Annotation\Reader\DoctrineReader,
    Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Class level documentation.
 *
 * @category Sonno
 * @package  Test
 */
class AnnotationDriverTest extends \PHPUnit_Framework_TestCase
{
    public function testParseConfig()
    {
        $pathAnnotation = $this
            ->getMockBuilder('Sonno\Annotation\Path')
            ->disableOriginalConstructor()
            ->getMock();
        $pathAnnotation
            ->expects($this->exactly(2))
            ->method('getPath')
            ->will($this->onConsecutiveCalls(
                '/messages',
                '/something'
            ));

        $consumesAnnotation = $this
            ->getMockBuilder('Sonno\Annotation\Consumes')
            ->disableOriginalConstructor()
            ->getMock();
        $consumesAnnotation
            ->expects($this->exactly(1))
            ->method('getMediaTypes')
            ->will($this->returnValue(array('application/xml')));

        $producesAnnotation = $this
            ->getMockBuilder('Sonno\Annotation\Produces')
            ->disableOriginalConstructor()
            ->getMock();
        $producesAnnotation
            ->expects($this->exactly(2))
            ->method('getMediaTypes')
            ->will($this->onConsecutiveCalls(
                array('application/xml'),
                array('application/json')
            ));

        $httpMethodAnnotation = $this
            ->getMockBuilder('Sonno\Annotation\GET')
            ->disableOriginalConstructor()
            ->getMock();
        $httpMethodAnnotation
            ->expects($this->exactly(1))
            ->method('__toString')
            ->will($this->returnValue('GET'));

        $pathParamAnnotation = $this
            ->getMockBuilder('Sonno\Annotation\PathParam')
            ->disableOriginalConstructor()
            ->getMock();
        $pathParamAnnotation
            ->expects($this->exactly(1))
            ->method('getParams')
            ->will($this->returnValue(array('id')));

        $contextAnnotation = $this
            ->getMockBuilder('Sonno\Annotation\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $contextAnnotation
            ->expects($this->exactly(1))
            ->method('getContext')
            ->will($this->returnValue('Request'));

        $reader = $this
            ->getMockBuilder('Sonno\Annotation\Reader\DoctrineReader')
            ->disableOriginalConstructor()
            ->getMock();
        $reader
            ->expects($this->exactly(3))
            ->method('getClassAnnotation')
            ->will($this->onConsecutiveCalls(
                $pathAnnotation,
                $consumesAnnotation,
                $producesAnnotation
            ));
        $reader
            ->expects($this->exactly(9))
            ->method('getMethodAnnotation')
            ->will($this->onConsecutiveCalls(
                $pathAnnotation,
                null,
                $producesAnnotation,
                $httpMethodAnnotation,
                $pathParamAnnotation,
                null,
                null,
                null,
                null
            ));
        $reader
            ->expects($this->exactly(1))
            ->method('getPropertyAnnotation')
            ->will($this->onConsecutiveCalls($contextAnnotation));

        $driver = new AnnotationDriver(
            array('Sonno\Test\Configuration\Driver\Asset\ValidResourceBasic'),
            $reader
        );
        $driver->parseConfig();
    }
}
