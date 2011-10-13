<?php

namespace Sonno\Test\Configuration\Driver\Asset;

/**
 * @Path("messages")
 */
class ValidResourceBasic
{
    /**
     * @Context('Request')
     */
    protected $_request;

    /**
     * @GET
     * @Produces("application/xml")
     */
    public function XmlCollection()
    {
        
    }

    /**
     * @GET
     * @Produces("application/xml")
     * @Path("{id}")
     * @PathParam("id")
     */
    // public function XmlItem()
    // {
    //     
    // }

    /**
     * @GET
     * @Produces("application/json")
     */
    // public function JsonCollection()
    // {
    //     
    // }

    /**
     * @GET
     * @Produces("application/json")
     * @Path("{id}")
     */
    // public function JsonItem()
    // {
    //     
    // }

    /**
     * @POST
     * @Consumes("application/xml")
     */
    // public function create()
    // {
    //     
    // }
}
