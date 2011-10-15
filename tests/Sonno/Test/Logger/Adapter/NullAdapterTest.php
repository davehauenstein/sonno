<?php

/**
 * @category Sonno
 * @package  Test
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Logger\Adapter;

use Sonno\Logger\Adapter\NullAdapter;

/**
 * Class level documentation.
 *
 * @category Sonno
 * @package  Test
 */
class NullAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testLoggerAdapterProxiesDebug()
    {
        $adapter = new NullAdapter();
        $this->assertTrue($adapter->debug('xxx'));
    }

    public function testLoggerAdapterProxiesInfo()
    {
        $adapter = new NullAdapter();
        $this->assertTrue($adapter->info('xxx'));
    }

    public function testLoggerAdapterProxiesNotice()
    {
        $adapter = new NullAdapter();
        $this->assertTrue($adapter->notice('xxx'));
    }

    public function testLoggerAdapterProxiesWarn()
    {
        $adapter = new NullAdapter();
        $this->assertTrue($adapter->warn('xxx'));
    }

    public function testLoggerAdapterProxiesErr()
    {
        $adapter = new NullAdapter();
        $this->assertTrue($adapter->err('xxx'));
    }

    public function testLoggerAdapterProxiesCrit()
    {
        $adapter = new NullAdapter();
        $this->assertTrue($adapter->crit('xxx'));
    }

    public function testLoggerAdapterProxiesAlert()
    {
        $adapter = new NullAdapter();
        $this->assertTrue($adapter->alert('xxx'));
    }

    public function testLoggerAdapterProxiesEmerg()
    {
        $adapter = new NullAdapter();
        $this->assertTrue($adapter->emerg('xxx'));
    }
}
