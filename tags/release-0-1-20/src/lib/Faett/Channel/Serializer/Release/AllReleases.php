<?php

/**
 * Faett_Channel_Serializer_Release_AllReleases
 *
 * NOTICE OF LICENSE
 * 
 * Faett_Channel is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Faett_Channel is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Faett_Channel.  If not, see <http://www.gnu.org/licenses/>.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Faett_Channel to newer
 * versions in the future. If you wish to customize Faett_Channel for your
 * needs please refer to http://www.faett.net for more information.
 *
 * @category   Faett
 * @package    Faett_Channel
 * @copyright  Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    <http://www.gnu.org/licenses/> 
 * 			   GNU General Public License (GPL 3)
 */

/**
 * @category   Faett
 * @package    Faett_Channel
 * @copyright  Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    <http://www.gnu.org/licenses/> 
 * 			   GNU General Public License (GPL 3)
 * @author     Tim Wagner <tw@faett.net>
 */
class Faett_Channel_Serializer_Release_AllReleases
    extends Faett_Channel_Serializer_Release_Abstract {

    /**
     * Passes the package the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Package $package
     * 		The package to serializer has to be attached to
     * @return void
     */
    public function __construct(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Package $package) {
        // set the passed package
        Faett_Channel_Serializer_Release_Abstract::__construct(
            $user,
            $package
        );
        // set the namespace
        $this->setNamespace(
            Faett_Channel_Serializer_Release_Abstract::REST_ALLRELEASES
        );
    }

    /**
     * (non-PHPdoc)
     * @see lib/Faett/Channel/Serializer/Interfaces/Faett_Channel_Serializer_Interfaces_Serializer#serialize()
     */
    public function serialize()
    {
        try {
            // initialize a new DOM document
            $doc = new DOMDocument('1.0', 'UTF-8');
            // create new namespaced root element
            $a = $doc->createElementNS(
            	$this->_namespace,
            	'a'
            );
            // add the schema to the root element
            $a->setAttributeNS(
            	'http://www.w3.org/2001/XMLSchema-instance',
            	'xsi:schemaLocation',
            	'http://pear.php.net/dtd/rest.allreleases http://pear.php.net/dtd/rest.allreleases.xsd'
            );
            // create an element for the package's name
            $p = $doc->createElement('p');
            $p->nodeValue = $this->_package->getPackageName();
            // append the element with the packages's name to the root element
            $a->appendChild($p);
            // create an element for the channel's name
            $c = $doc->createElement('c');
            $c->nodeValue = Mage::helper('channel')->getChannelName();
            // append the element with the channel's name to the root element
            $a->appendChild($c);
            // check if releases are available
            $hasLinks = $this->_package->getTypeInstance(true)->hasLinks(
                $this->_package
            );
            // if releases are available
            if ($hasLinks) {
                // load the releases
                $links = $this->_package->getTypeInstance(true)->getLinks(
                    $this->_package
                );
                // iterate over the channel's categories
                foreach ($links as $link) {
                    // create an element for each release
                    $r = $doc->createElement('r');
                    // create an element for the version number
                    $v = $doc->createElement('v');
                    $v->nodeValue = $link->getVersion();
                    $r->appendChild($v);
                    // create an element for the versions stability
                    $s = $doc->createElement('s');
                    $s->nodeValue = $link->getState();
                    $r->appendChild($s);
                    // append the element to the root element
                    $a->appendChild($r);
                }
            }
            // append the root element to the DOM tree
            $doc->appendChild($a);
            // return the XML document
            return $doc->saveXML();
        } catch(Exception $e) {
            return $e->getMessage();
        }
    }
}