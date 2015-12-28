<?php

/**
 * Faett_Channel_Serializer_Package_Maintainers
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
class Faett_Channel_Serializer_Package_Maintainers
    extends Faett_Channel_Serializer_Package_Abstract {

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
        Faett_Channel_Serializer_Package_Abstract::__construct(
            $user,
            $package
        );
        // set the namespace
        $this->setNamespace(
            Faett_Channel_Serializer_Package_Abstract::REST_PACKAGEMAINTAINERS
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
            $m = $doc->createElementNS(
            	$this->_namespace,
            	'm'
            );
            // add the schema to the root element
            $m->setAttributeNS(
            	'http://www.w3.org/2001/XMLSchema-instance',
            	'xsi:schemaLocation',
            	'http://pear.php.net/dtd/rest.packagemaintainers http://pear.php.net/dtd/rest.packagemaintainers.xsd'
            );
            // create an element for the package's name
            $p = $doc->createElement('p');
            $p->nodeValue = $this->_package->getPackageName();
            $m->appendChild($p);
            // create an element for the packages's channel name
            $c = $doc->createElement('c');
            $c->nodeValue = Mage::helper('channel')->getChannelName();
            $m->appendChild($c);
            // add the package maintainers
            foreach ($this->_package->getMaintainers() as $maintainer) {
                // create an element for the package's maintainers
                $mm = $doc->createElement('m');
                // create an element for the maintainers handle
                $h = $doc->createElement('h');
                $h->nodeValue = $maintainer->getUsername();
                $mm->appendChild($h);
                // create an element if the maintainer is active
                $a = $doc->createElement('a');
                $a->nodeValue = $maintainer->getActive();
                $mm->appendChild($a);
                // append the maintainer itself to the root element
                $m->appendChild($mm);
            }
            // append the root element to the DOM tree
            $doc->appendChild($m);
            // return the XML document
            return $doc->saveXML();
        } catch(Exception $e) {
            return $e->getMessage();
        }
    }
}