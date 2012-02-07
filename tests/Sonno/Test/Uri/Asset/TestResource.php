<?php

/**
 * @category Sonno
 * @package  Sonno\Test\Uri\Asset
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Uri\Asset;

/**
 * @category Sonno
 * @package  Sonno\Test\Uri\Asset
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 *
 * @Path("/test/resource")
 * @Produces("application/xml")
 */
class TestResource
{
    /**
     * @GET
     */
    public function getCollection()
    {
    }

    /**
     * @GET
     * @Path("{id}")
     */
    public function getEntity()
    {
    }

    /**
     * @GET
     * @Path("{id}/subentities")
     */
    public function getSubEntities()
    {
    }
}

