<?php

/**
 * @category Sonno
 * @package  Test
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Configuration;

use Sonno\Configuration\Configuration,
    Sonno\Configuration\Route;

/**
 * Class level documentation.
 *
 * @category Sonno
 * @package  Test
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testBasePathGetterAndSetterMethods()
    {
        $config = new Configuration();
        $config->setBasePath('some/base/path');
        $this->assertEquals('/some/base/path', $config->getBasePath());

        $config->setBasePath('/some/other/base/path/');
        $this->assertEquals('/some/other/base/path', $config->getBasePath());
    }
}
