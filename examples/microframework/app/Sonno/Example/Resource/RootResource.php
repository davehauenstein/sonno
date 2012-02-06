<?php

/**
 * @category  Sonno
 * @package   Sonno\Example\Resource
 * @author    Dave Hauenstein <davehauenstein@gmail.com>
 * @author    Tharsan Bhuvanendran <me@tharsan.com>
 * @author    360i <sonno@360i.com>
 * @copyright Copyright (c) 2011 360i LLC (http://360i.com)
 * @license   http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Example\Resource;

/**
 * This assumes that Twig is already in the PHP include path (e.g. installed
 * via PEAR)
 */
require_once 'Twig/Autoloader.php';

use Sonno\Http\Response\Response,
    Sonno\Annotation\Path,
    Sonno\Annotation\GET,
    Sonno\Annotation\POST,
    Sonno\Annotation\Context,
    Sonno\Annotation\Produces,
    Sonno\Annotation\Consumes,
    Sonno\Annotation\PathParam,
    Twig_Autoloader,
    Twig_Loader_Filesystem,
    Twig_Environment;

class RootResource
{
    /**
     * The incoming HTTP request, injected by Sonno.
     *
     * @Context("Request")
     */
    protected $_request;

    /**
     * The Twig Environment used for generating views.
     *
     * @var Twig_Environment
     */
    protected $_twig;

    /**
     * Intialization of the root resource class.
     * Using the default Twig_Environment configuration, but this should
     * normally be configured with template caching and other good stuff.
     */
    public function __construct()
    {
        Twig_Autoloader::register();
        $loader      = new Twig_Loader_Filesystem(APPLICATION_PATH . '/views');
        $this->_twig = new Twig_Environment($loader);
    }

    /**
     * Root resource.
     *
     * @GET
     * @Path("/")
     */
    public function get()
    {
        $tpl = $this->_twig->loadTemplate('main.twig.html');
        return $tpl->render(array());
    }

    /**
     * Contact Us page.
     *
     * @GET
     * @Path("/contact")
     */
    public function contact()
    {
        $tpl = $this->_twig->loadTemplate('contact.twig.html');
        return $tpl->render(array());
    }
}
