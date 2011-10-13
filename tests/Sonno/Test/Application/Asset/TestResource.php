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
    protected $incomingRequest;

    public function modifyString($str, $op)
    {
        if ('upper' == $op) {
            return strtoupper($str) . '|' . $this->incomingRequest->getMethod();
        } else if ('lower' == $op) {
            return strtolower($str) . '|' . $this->incomingRequest->getMethod();
        } else {
            return $str . '|' . $this->incomingRequest->getMethod();
        }
    }

    public function randomArray()
    {
        return array('random' => rand());
    }

    public function randomResponse()
    {
        return new Response(200, 'random response content');
    }

    public function getPolo($colour)
    {
        return new Polo($colour);
    }
}