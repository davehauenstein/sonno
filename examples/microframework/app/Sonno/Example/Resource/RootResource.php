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
    Sonno\Annotation\FormParam,
    Sonno\Annotation\PathParam,
    Twig_Autoloader,
    Twig_Environment,
    Twig_Function_Function,
    Twig_Loader_Filesystem;

/**
 * The root resource class is effectively a single controller class (in a
 * traditional MVC paradigm) whose class methods are actions that produce an
 * HTML representation for each page of our microsite.
 * 
 * Typically, a class method will simply render a Twig template to produce the
 * required HTML document representation.
 *
 * @Path("/")
 * @Produces({"text/html"})
 */
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
     * Home page.
     * This is static markup rendered by the Twig templating engine.
     *
     * @GET
     */
    public function home()
    {
        $tpl = $this->_twig->loadTemplate('home.twig');
        return $tpl->render(array());
    }

    /**
     * About page.
     * This is static markup rendered by the Twig templating engine.
     *
     * @GET
     * @Path("/about")
     */
    public function about()
    {
        $tpl = $this->_twig->loadTemplate('about.twig');
        return $tpl->render(array());
    }

    /**
     * Contact page.
     * This is static markup rendered by the Twig templating engine.
     *
     * @GET
     * @Path("/contact")
     */
    public function contact()
    {
        $tpl = $this->_twig->loadTemplate('contact.twig');
        return $tpl->render(array());
    }

    /**
     * Contact submission page.
     * Accepts POST requests with an entity body as
     * application/x-www-form-urlencoded which is the default MIME type sent by
     * a web browser when you submit a form.
     * The request entity can be parsed using the PHP native function parse_str
     * which parses a URL-encoded set of key/value pairs. This exposes the form
     * data submitted by the web browser.
     *
     * @POST
     * @Path("/contact")
     * @Consumes({"application/x-www-form-urlencoded"})
     * @Produces({"text/plain"})
     * @FormParam("fullname")
     */
    public function contactsubmit($fullname) {
        return "Thanks for sending us your input, $fullname!";
    }
}
