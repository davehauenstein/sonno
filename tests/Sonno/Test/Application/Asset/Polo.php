<?php

/**
 * @category Sonno
 * @package  Sonno\Test\Application\Asset
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Application\Asset;

use Sonno\Application\Renderable,
    Sonno\Http\Variant;

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

    public function render(Variant $mediaType)
    {
        return $this->colour;
    }

    public function unrender($representation, Variant $mediaType)
    {
    }
}
