Sonno - A RESTful PHP Framework!
================================

Sonno is a lightweight PHP framework based loosely on
[Jersey](http://jersey.java.net/ "Jersey"), a Java ReST framework reference
implementation of the [JAX-RS](http://jcp.org/en/jsr/detail?id=311 "JAX-RS")
specification.

Plain-Old-PHP objects are configured with a set of annotations describing
a set of Resources.

``` php

<?php

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
 * @Path("/")
 */
class HelloResource
{
    /**
     * @Context("Request")
     */
    protected $_request;

    /**
     * @GET
     * @Produces({"text/plain"})
     */
    public function getHelloWorld()
    {
        return 'Hello ReSTful World!';
    }

    /**
     * @GET
     * @Path("/users")
     * @Produces({"application/xml"})
     */
    public function getUserCollection()
    {
        return new UserCollection('application/xml');
    }

    /**
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
}
```

License
-------

Sonno is licensed under the New BSD license. A copy can be
found here:
[http://sonno.360i.com/LICENSE.txt](http://sonno.360i.com/LICENSE.txt)

Dependencies
------------

The current version of Sonno supports configuring a ReSTful web application
via annotations. Rather than reinvent the wheel and develop our own annotation
reader, we've provided interfaces and adapters. The adapter that ships with
this version uses Doctrine-Common version >= 2.1.1. That's not to say that any
developer couldn't implement their own annotation reader, then build an adapter
that implements Sonno\Annotation\Reader\ReaderInterface.

Here's an example of how a Sonno application can be configured using Doctrine's
annotation reader:

``` php
<?php

use Sonno\Configuration\Driver\AnnotationDriver,
    Sonno\Annotation\Reader\DoctrineReader,
    Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\Common\Annotations\AnnotationRegistry;

$doctrineReader = new AnnotationReader();
AnnotationRegistry::registerAutoloadNamespace(
    'Sonno\Annotation',
    realpath('path/to/Sonno/src')
);

$annotationReader = new DoctrineReader($doctrineReader);
$resources = array(
    'FQNS\To\SomeResource',
    'FQNS\To\SomeOtherResource',
);

$driver = new AnnotationDriver($resources, $annotationReader);
$config = $driver->parseConfig();
```

Summary of Annotations
-----------------------

<table>
    <tr>
        <td>Annotation</td>
        <td>Target</td>
        <td>Description</td>
    </tr>
    <tr>
        <td>@Path</td>
        <td>Class, Method</td>
        <td>
            Specifies a relative path for a resource. When used on a class
            this annotation identifies that class as a root resource.
            When used on a method this annotation identifies a sub-resource
            method or locator.
        </td>
    </tr>
    <tr>
        <td>@Consumes</td>
        <td>Class, Method</td>
        <td>Specifies a list of media types that can be consumed.</td>
    </tr>
    <tr>
        <td>@Produces</td>
        <td>Class, Method</td>
        <td>Specifies a list of media types that can be produced.</td>
    </tr>
    <tr>
        <td>@Context</td>
        <td>Property</td>
        <td>Identifies an injection target for a Request object.</td>
    </tr>
    <tr>
        <td>@DELETE</td>
        <td>Method</td>
        <td>
            Specifies that the annotated method handles HTTP DELETE requests.
        </td>
    </tr>
    <tr>
        <td>@GET</td>
        <td>Method</td>
        <td>
            Specifies that the annotated method handles HTTP GET requests.
        </td>
    </tr>
    <tr>
        <td>@HEAD</td>
        <td>Method</td>
        <td>
            Specifies that the annotated method handles HTTP HEAD requests.
        </td>
    </tr>
    <tr>
        <td>@OPTIONS</td>
        <td>Method</td>
        <td>
            Specifies that the annotated method handles HTTP OPTIONS requests.
        </td>
    </tr>
    <tr>
        <td>@POST</td>
        <td>Method</td>
        <td>
            Specifies that the annotated method handles HTTP POST requests.
        </td>
    </tr>
    <tr>
        <td>@PUT</td>
        <td>Method</td>
        <td>
            Specifies that the annotated method handles HTTP PUT requests.
        </td>
    </tr>
    <tr>
        <td>@PathParam</td>
        <td>Method</td>
        <td>
            Specifies that the value of a method parameter is to be extracted
            from the request URI path. The value of the annotation identifies
            the name of a URI template parameter.
        </td>
    </tr>
    <tr>
        <td>@QueryParam</td>
        <td>Method</td>
        <td>
            Specifies that the value of a method parameter is to be extracted
            from a URI query parameter. The value of the annotation identifies
            the name of a query parameter.
        </td>
    </tr>
    <tr>
        <td>@CookieParam</td>
        <td>Method</td>
        <td>
            Specifies that the value of a method parameter is to be extracted
            from a HTTP cookie. The value of the annotation identifies the
            name of a the cookie.
        </td>
    </tr>
    <tr>
        <td>@FormParam</td>
        <td>Method</td>
        <td>
            Specifies that the value of a method parameter is to be extracted
            from a form parameter in a request entity body. The value of the
            annotation identifies the name of a form parameter.
            Note that whilst the annotation target allows use on fields and
            methods, the specification only requires support for use on
            resource method parameters.
        </td>
    </tr>
    <tr>
        <td>@HeaderParam</td>
        <td>Method</td>
        <td>
            Specifies that the value of a method parameter is to be extracted
            from a HTTP header. The value of the annotation identifies the
            name of a HTTP header.
        </td>
    </tr>
</table>

Contribution Guidelines
-----------------------

### Coding Standard ###

We strictly adhere to the Zend Framework coding standard. Before pushing to the
origin, you should run
[PHP_CodeSniffer](http://pear.php.net/package/PHP_CodeSniffer "phpcs"):
`$ phpcs --standard=zend src`. All errors and warnings
should be cleaned up before making a pull request.

### Add Test Cases ###

We try to keep our Code Coverage high and our CRAP index low. Before submitting
a pull request, run the PHPUnit test suite and ensure that all tests pass.
If you add functionality or fix a bug, please include test cases to cover the
code you've sumitted.

Additional Resources
--------------------

 - [Api Documentation](http://sonno.360i.com/docs/api "Api Docs")
 - [Copy of the license](http://sonno.360i.com/LICENSE.txt "License")
