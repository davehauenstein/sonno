<?php

/**
 * @category Sonno
 * @package  Sonno\Annotation
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 * @author     360i <sonno@360i.com>
 * @copyright  Copyright (c) 2011 360i LLC (http://360i.com)
 * @license    http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Annotation;

/**
 *
 * @Annotation
 * @category Sonno
 * @package  Sonno\Annotation
 */
abstract class ParameterContainer
{
    /**
     * An array of parameters.
     *
     * @var array
     */
    protected $_params = array();

    /**
     * Construct a new Parameter instance.
     *
     * @param $params string|array A list of parameters.
     */
    public function __construct($params)
    {
        if (isset($params['value'])) {
            $params = $params['value'];
        }
        $this->_params = (array) $params;
    }

    /**
     * Getter for _params property.
     *
     * @return array Value of _params property.
     */
    public function getParams()
    {
        return $this->_params;
    }
}
