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
 * This annotation is used to inject information into a class field. This
 * annotation is only available to instance variables.
 *
 * @Annotation
 * @category Sonno
 * @package  Sonno\Annotation
 */
class Context
{
    /**
     * The context value. Will be one of $_availableContexts.
     *
     * @var string
     */
    protected $_context;

    /**
     * A list of supported contexts.
     *
     * @var array
     */
    protected $_availableContexts = array(
        'Request',
        'UriInfo',
    );

    /**
     * Construct a new Context instance.
     *
     * @param $context string A supported context.
     * @throws \InvalidArgumentException If the $context is not supported.
     */
    public function __construct($context)
    {
        if (is_array($context) && isset($context['value'])) {
            $context = $context['value'];
        }
        if (!in_array($context, $this->_availableContexts)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Context must be one of the following %s, provided was %s',
                    implode(', ', $this->_availableContexts),
                    $context
                )
            );
        }
        $this->_context = $context;
    }

    /**
     * Return the value of the _context property.
     *
     * @return string A supported context value.
     */
    public function getContext()
    {
        return $this->_context;
    }
}
