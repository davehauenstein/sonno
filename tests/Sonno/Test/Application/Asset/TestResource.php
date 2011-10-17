<?php

/**
 * @category Sonno
 * @package  Sonno\Test\Application\Asset
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Application\Asset;

require_once __DIR__ . '/Polo.php';

use Sonno\Http\Response\Response;

/**
 * @category Sonno
 * @package  Sonno\Test\Application\Asset
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */
class TestResource
{
    protected $_incomingRequest;
    protected $_defaultResponseText;

    public function __construct($arg0 = 'random response content')
    {
        $this->_defaultResponseText = $arg0;
    }

    public function modifyString($str, $op)
    {
        if ('upper' == $op) {
            return strtoupper($str) . '|' . $this->_incomingRequest->getMethod();
        } else if ('lower' == $op) {
            return strtolower($str) . '|' . $this->_incomingRequest->getMethod();
        } else {
            return $str . '|' . $this->_incomingRequest->getMethod();
        }
    }

    public function randomArray()
    {
        return array('random' => rand());
    }

    public function randomResponse()
    {
        return new Response(200, $this->_defaultResponseText);
    }

    public function getPolo($colour)
    {
        return new Polo($colour);
    }
}