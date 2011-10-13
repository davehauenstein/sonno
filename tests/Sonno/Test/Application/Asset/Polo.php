<?php

/**
 * @category Sonno
 * @package  Sonno\Test\Application\Asset
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Application\Asset;

use Sonno\Application\Renderable;

/**
 * @category Sonno
 * @package  Sonno\Test\Application\Asset
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */
class Polo implements Renderable
{
    protected $colour;

    public function __construct($colour)
    {
        $this->colour = $colour;
    }

    public function slideOffSlowly()
    {
    }

    public function render($mediaType)
    {
        return $this->colour;
    }
}
