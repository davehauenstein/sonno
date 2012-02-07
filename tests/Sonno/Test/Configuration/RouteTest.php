<?php

/**
 * @category Sonno
 * @package  Test
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Configuration;

use Sonno\Configuration\Route;

/**
 * Class level documentation.
 *
 * @category Sonno
 * @package  Test
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function testPathSetterAndGetter()
    {
        $route = new Route(array(
            'classPath'  => 'some',
            'methodPath' => '/base/path/'
        ));
        $this->assertEquals('/some/base/path', $route->getPath());

        $route = new Route(array(
            'classPath'  => '/some/other/',
            'methodPath' => 'base/path'
        ));
        $this->assertEquals('/some/other/base/path', $route->getPath());
    }
    
    public function testFormSetterAndGetter()
    {
    	$route = new Route(array(
           'formParams' => array('test' => 'true'),
        ));
    	$this->assertEquals(array('test'=>'true'), $route->getFormParams());

    	$route = new Route(array(
           'formParams' => array('notatest' => 'true'),
        ));
    	$this->assertEquals(array('notatest'=>'true'), $route->getFormParams());
    }

    public function testHttpMethodSetterAndGetter()
    {
        $route = new Route(array(
            'httpMethod' => 'GET'
        ));
        $this->assertEquals('GET', $route->getHttpMethod());

        $route = new Route(array(
            'httpMethod' => 'post'
        ));
        $this->assertEquals('POST', $route->getHttpMethod());
    }
}
