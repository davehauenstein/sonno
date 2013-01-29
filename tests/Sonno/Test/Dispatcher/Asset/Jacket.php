<?php

/**
 * @category Sonno
 * @package  Sonno\Test\Application\Asset
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Dispatcher\Asset;

use Sonno\Application\Renderable,
    Sonno\Http\Variant;

/**
 * @category Sonno
 * @package  Sonno\Test\Dispatcher\Asset
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */
class Jacket implements Renderable
{
    protected $colour;
    protected $brand;

    public function __construct($colour, $brand)
    {
        $this->colour = $colour;
        $this->brand = $brand;
    }

    public function getColour()
    {
        return $this->colour;
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function render(Variant $mediaType)
    {
        return sprintf(
            '<jacket><colour>%s</colour><brand>%s</brand>',
            $this->colour,
            $this->brand
        );
    }

    public static function unrender($representation, Variant $mediaType)
    {
        $data = json_decode($representation);
        return new static($data->colour, $data->brand);
    }
}
