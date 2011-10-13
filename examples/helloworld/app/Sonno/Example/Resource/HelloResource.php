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

use Sonno\Http\Response\Response,
    Sonno\Annotation\Path,
    Sonno\Annotation\GET,
    Sonno\Annotation\POST,
    Sonno\Annotation\Context,
    Sonno\Annotation\Produces,
    Sonno\Annotation\Consumes,
    Sonno\Annotation\PathParam,
    Sonno\Example\Representation\User\Collection as UserCollection;

/**
 * Welcome to the "Hello ReSTful World" example resource! This class will
 * display how to use annotations to mark up a resource class. This resource
 * will show the basic "Hello World", as well as how to represent collections
 * and items using "Users" as the example. All methods are documented to
 * explain how the annotations are used.
 *
 * The Path annotation, when applied to a class, will be prepended to all
 * methods Path annotations.
 *
 * @Path("/")
 */
class HelloResource
{
    /**
     * The Context annotation can be used to inject objects into class
     * properties. The current release only supports injecting a Request
     * object using the Context annotation.
     *
     * The Request object will represent the current incoming HTTP request.
     *
     * @Context("Request")
     */
    protected $_request;

    /**
     * The first example presented here will handle HTTP GET requests to the
     * root resource. Notice that there is no Path annotation. This works
     * because the Path is inherited from the Path annotation specified at the
     * class level.
     *
     * The next thing to notice is the Produces annotation. If this annotation
     * is not provided, the default is a wildcard media type and subtype. Since
     * we're providing 'text/plain' as our media type, the content
     * negotiation algoritm will check the Accept header to make sure that
     * the client can handle this media type. If not, a 406 Not Acceptable
     * will be returned.
     *
     * Finally, this method returns a string. There are 3 types of acceptable
     * return types. First is any PHP scalar (integer, float, string,
     * or boolean). Second is an instace of Sonno\Http\Response, which will be
     * demonstrated in another method below. Last is any class which implements
     * Sonno\Application\Renderable.
     *
     * @GET
     * @Produces({"text/plain"})
     */
    public function getHelloWorld()
    {
        return 'Hello ReSTful World!';
    }

    /**
     * The first thing to notice here, that's different from the above method,
     * is the Path annotation. The preceding "/" is not necessary, nor will it
     * actually do anything. No matter what, the Path annotation that is
     * applied to a method is appended to the class Path annotation.
     *
     * The other difference is the function body. It uses a class that
     * implements the Sonno\Application\Renderable interface.
     *
     * @GET
     * @Path("/users")
     * @Produces({"application/xml"})
     */
    public function getUserCollection()
    {
        return new UserCollection('application/xml');
    }

    /**
     * This is the first example in this Resource that doesn't use a GET
     * annotation. Here, the resource will handle HTTP POST requests that are
     * sent in 'application/xml' form. Also, this method will also handle
     * requests to the same Path as the method above. The router determines
     * what resource method to call, not only based on the Path annotation, but
     * also what the HTTP method is as well as the Produces annotations.
     *
     * Another annotation that's being introduced here is the Consumes
     * annotation. This specifies what type of content it can accept based on
     * what the client's "Content-Type" header is set to. If the client
     * specifies a type not supported by a resource, a 415 Unsupported Media
     * Type is set, and this function is never executed.
     *
     * Looking at the function body, we're also using the property that was
     * injected via the Context annotation with a Sonno\Http\Request\Request
     * object. We use this object to pull the raw XML data out of the request
     * body.
     *
     * Finally, take notice that the function is returning a Response object.
     * But before the object is returned the method "setCreated" is called.
     * This will set the location header with the argument passed into the
     * constructor as well as set the response code to 201. There are several
     * other convenience methods on the Response object that can be used for
     * similar purposes.
     *
     * @POST
     * @Path("/users")
     * @Consumes({"application/xml"})
     */
    public function saveUserXml()
    {
        // Retrieve the request body.
        $data = $this->_request->getRequestBody();

        // ... Do some processing of $data, then save it...

        $response = new Response();
        return $response->setCreated('http://example.sonno.dev/users/10');
    }

    /**
     * This example illustrates how the Path annotation can be configured
     * with variable parameters. If you specify a path segment between curly
     * braces, it will be used to match against a variable parameter. It will
     * only be injected as an argument of the annotated method if the PathParam
     * annotation is specified as well. The value of the PathParam annotation
     * will be used to match against the name of the variable between the curly
     * braces. The name of the argument of the method must also correspond.
     *
     * The body of this function just builds an XML tree using DOMDocument and
     * returns a string. The ID node in the XML document will be based on
     * whatever is specified in the PathParam.
     *
     * @GET
     * @Path("/users/{id}")
     * @PathParam("id")
     * @Produces({"application/xml"})
     */
    public function getUserXml($id)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $root   = $dom->createElement('User');
        $root   = $dom->appendChild($root);
        $userId = $dom->createElement('ID');
        $userId = $root->appendChild($userId);
        $text   = $dom->createTextNode($id);
        $text   = $userId->appendChild($text);
        $fName  = $dom->createElement('FirstName');
        $fName  = $root->appendChild($fName);
        $text   = $dom->createTextNode('Dave');
        $text   = $fName->appendChild($text);
        $lName  = $dom->createElement('LastName');
        $lName  = $root->appendChild($lName);
        $text   = $dom->createTextNode('Hauenstein');
        $text   = $lName->appendChild($text);
        return $dom->saveXML();
    }
}
