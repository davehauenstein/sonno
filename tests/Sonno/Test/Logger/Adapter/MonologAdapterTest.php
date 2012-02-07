<?php

/**
 * @category Sonno
 * @package  Test
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Logger\Adapter;

use Sonno\Logger\Adapter\MonologAdapter;

require_once __DIR__ . '/Asset/MonologAsset.php';

/**
 * Class level documentation.
 *
 * @category Sonno
 * @package  Test
 */
class MonologAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testLoggerAdapterProxiesDebug()
    {
        $logger = $this
            ->getMockBuilder('Monolog\Logger')
            ->getMock();

        $logger
            ->expects($this->exactly(1))
            ->method('debug')
            ->will($this->returnValue(true));

        $adapter = new MonologAdapter($logger);
        $this->assertTrue($adapter->debug('xxx'));
    }

    public function testLoggerAdapterProxiesInfo()
    {
        $logger = $this
            ->getMockBuilder('Monolog\Logger')
            ->getMock();

        $logger
            ->expects($this->exactly(1))
            ->method('info')
            ->will($this->returnValue(true));

        $adapter = new MonologAdapter($logger);
        $this->assertTrue($adapter->info('xxx'));
    }

    public function testLoggerAdapterProxiesNotice()
    {
        $logger = $this
            ->getMockBuilder('Monolog\Logger')
            ->getMock();

        $logger
            ->expects($this->exactly(1))
            ->method('notice')
            ->will($this->returnValue(true));

        $adapter = new MonologAdapter($logger);
        $this->assertTrue($adapter->notice('xxx'));
    }

    public function testLoggerAdapterProxiesWarn()
    {
        $logger = $this
            ->getMockBuilder('Monolog\Logger')
            ->getMock();

        $logger
            ->expects($this->exactly(1))
            ->method('warn')
            ->will($this->returnValue(true));

        $adapter = new MonologAdapter($logger);
        $this->assertTrue($adapter->warn('xxx'));
    }

    public function testLoggerAdapterProxiesErr()
    {
        $logger = $this
            ->getMockBuilder('Monolog\Logger')
            ->getMock();

        $logger
            ->expects($this->exactly(1))
            ->method('err')
            ->will($this->returnValue(true));

        $adapter = new MonologAdapter($logger);
        $this->assertTrue($adapter->err('xxx'));
    }

    public function testLoggerAdapterProxiesCrit()
    {
        $logger = $this
            ->getMockBuilder('Monolog\Logger')
            ->getMock();

        $logger
            ->expects($this->exactly(1))
            ->method('crit')
            ->will($this->returnValue(true));

        $adapter = new MonologAdapter($logger);
        $this->assertTrue($adapter->crit('xxx'));
    }

    public function testLoggerAdapterProxiesAlert()
    {
        $logger = $this
            ->getMockBuilder('Monolog\Logger')
            ->getMock();

        $logger
            ->expects($this->exactly(1))
            ->method('alert')
            ->will($this->returnValue(true));

        $adapter = new MonologAdapter($logger);
        $this->assertTrue($adapter->alert('xxx'));
    }

    public function testLoggerAdapterProxiesEmerg()
    {
        $logger = $this
            ->getMockBuilder('Monolog\Logger')
            ->getMock();

        $logger
            ->expects($this->exactly(1))
            ->method('emerg')
            ->will($this->returnValue(true));

        $adapter = new MonologAdapter($logger);
        $this->assertTrue($adapter->emerg('xxx'));
    }
}
