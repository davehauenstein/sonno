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
 * Identifies the URI path that a resource class or class method will serve
 * requests for.
 *
 * Paths are relative. For an annotated class the base URI is the application
 * context. For an annotated method the base URI is the effective URI of the
 * containing class. For the purposes of absolutizing a path against the base
 * URI , a leading '/' in a path is ignored and base URIs are treated as if
 * they ended in '/'.
 *
 * @Annotation
 * @category Sonno
 * @package  Sonno\Annotation
 */
class Path
{
    /**
     * URI path of a resource class or method.
     *
     * @var string
     */
    protected $_path;

    /**
     * Construct a new Path annotation.
     *
     * @param $path string URI path of a resource class or method.
     */
    public function __construct($path)
    {
        if (is_array($path) && isset($path['value'])) {
            $path = $path['value'];
        }
        $this->_path = '/' . trim($path, '/');
    }

    /**
     * Getter for _path property.
     *
     * @return $path string URI path of a resource class or method.
     */
    public function getPath()
    {
        return $this->_path;
    }
}
