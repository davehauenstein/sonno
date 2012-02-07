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
    Twig_Environment,
    Twig_Function_Function,
    Twig_Loader_Filesystem;

class RootResource
{
    /**
     * The incoming HTTP request, injected by Sonno.
     *
     * @Context("Request")
     */
    protected $_request;

    /**
     * @Context("UriInfo")
     */
    protected $_uriInfo;

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
     * @Produces({"text/html"})
     */
    public function home()
    {
        $tpl = $this->_twig->loadTemplate('home.twig');
        return $tpl->render(array());
    }

    /**
     * About page.
     *
     * @GET
     * @Path("/about")
     * @Produces({"text/html"})
     */
    public function about()
    {
        $tpl = $this->_twig->loadTemplate('about.twig');
        return $tpl->render(array());
    }

    /**
     * Contact Us page.
     *
     * @GET
     * @Path("/contact")
     * @Produces({"text/html"})
     */
    public function contact()
    {
        $tpl = $this->_twig->loadTemplate('contact.twig');
        return $tpl->render(array());
    }

    /**
     * Contact Us page - submission.
     *
     * @POST
     * @Path("/contact")
     * @Consumes({"application/x-www-form-urlencoded"})
     * @Produces({"text/html"})
     */
    public function contactsubmit() {
        parse_str($this->_request->getRequestBody(), $req);

        $builder = $this->_uriInfo->getAbsolutePathBuilder();
        echo $builder->build();

        return 'Thanks for sending us your input, ' . $req['fullname'] . '!';
    }
}
