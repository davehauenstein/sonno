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

    public function getColour()
    {
        return $this->colour;
    }

    public function slideOffSlowly()
    {
    }

    public function render(Variant $mediaType)
    {
        return $this->colour;
    }

    public static function unrender($representation, Variant $mediaType)
    {
        if ('application/json' == $mediaType->getMediaType())
        {
            $data = json_decode($representation);
            return new static($data->colour);
        }

        return null;
    }
}
