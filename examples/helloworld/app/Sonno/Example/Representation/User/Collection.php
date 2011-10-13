<?php

/**
 * @category  Sonno
 * @package   Sonno\Example\Representation\User
 * @author    Dave Hauenstein <davehauenstein@gmail.com>
 * @author    Tharsan Bhuvanendran <me@tharsan.com>
 * @author    360i <sonno@360i.com>
 * @copyright Copyright (c) 2011 360i LLC (http://360i.com)
 * @license   http://sonno.360i.com/LICENSE.txt New BSD License
 */

namespace Sonno\Example\Representation\User;

use Sonno\Application\Renderable;

/**
 * Represents a user collection.
 *
 * @category Sonno
 * @package  Sonno\Example\Representation\User
 */
class Collection implements Renderable
{
    /**
     * Create a representation of this object in the given media type.
     *
     * @param  $mediaType The media (MIME) type to produce a representation in.
     * @return mixed A scalar value.
     */
    public function render($mediaType)
    {
        if($mediaType != 'application/xml') {
            throw new \Exception(
                'Cannot process anything other than application/xml'
            );
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $root   = $dom->createElement('Users');
        $root   = $dom->appendChild($root);
        foreach ($this->_getUsers() as $userInfo) {
            $user = $dom->createElement('User');
            $user = $root->appendChild($user);

            $userId = $dom->createElement('ID');
            $userId = $user->appendChild($userId);
            $text   = $dom->createTextNode($userInfo['id']);
            $text   = $userId->appendChild($text);
            $fName  = $dom->createElement('FirstName');
            $fName  = $user->appendChild($fName);
            $text   = $dom->createTextNode($userInfo['fname']);
            $text   = $fName->appendChild($text);
            $lName  = $dom->createElement('LastName');
            $lName  = $user->appendChild($lName);
            $text   = $dom->createTextNode($userInfo['lname']);
            $text   = $lName->appendChild($text);
        }

        return $dom->saveXML();
    }

    /**
     * Return a list of users.
     *
     * @return array List of users.
     */
    protected function _getUsers()
    {
        return array(
            array(
                'id'    => 1,
                'fname' => 'Dave',
                'lname' => 'Hauenstein',
            ),
            array(
                'id'    => 2,
                'fname' => 'Henry',
                'lname' => 'Ford',
            ),
            array(
                'id'    => 3,
                'fname' => 'Abe',
                'lname' => 'Lincoln',
            ),
        );
    }
}
