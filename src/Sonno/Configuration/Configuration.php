<?php

/**
 * @category  Sonno
 * @package   Sonno\Configuration
 * @author    Dave Hauenstein <davehauenstein@gmail.com>
 * @author    Tharsan Bhuvanendran <me@tharsan.com>
 * @author    360i <sonno@360i.com>
 * @copyright Copyright (c) 2011 360i LLC (http://360i.com)
 * @license   http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Configuration;

use Sonno\Configuration\Route;

/**
 * Configuration encapsulates all necessary configuration parameters for a web
 * service.
 *
 * @category Sonno
 * @package  Sonno\Configuration
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 */
class Configuration
{
    /**
     * A base path to prepend to all configured routes.
     *
     * @var string
     */
    protected $_basePath;

    /**
     * An array of Route objects.
     *
     * var array<Sonno\Configuration\Route>
     */
    protected $_routes = array();

    /**
     * Setter for _basePath property. Will trim trailing forward slash '/'
     * and ensure a forward slash '/' exists at the beginning of the string.
     *
     * @param $basePath string Application base path.
     * @return Sonno\Configuration\Configuration Implements a fluent
     *         interface.
     */
    public function setBasePath($basePath)
    {
        $this->_basePath = '/' . trim($basePath, '/');
        return $this;
    }

    /**
     * Getter for _basePath property.
     *
     * @return string _basePath property.
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }

    /**
     * Add a route to the _routes property.
     * 
     * @return Sonno\Configuration\Configuration Implements a fluent
     *         interface.
     */
    public function addRoute(Route $route)
    {
        $this->_routes[] = $route;
        return $this;
    }

    /**
     * Returns an array of all routes.
     *
     * @return array<Sonno\Configuration\Route>
     */
    public function getRoutes()
    {
        return $this->_routes;
    }
}
