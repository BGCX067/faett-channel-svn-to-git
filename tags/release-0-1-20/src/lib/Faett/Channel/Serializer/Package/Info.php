<?php

/**
 * Faett_Channel_Serializer_Package_Info
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
class Faett_Channel_Serializer_Package_Info
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
            Faett_Channel_Serializer_Package_Abstract::REST_PACKAGE
        );
    }

    /**
     * (non-PHPdoc)
     * @see lib/Faett/Channel/Serializer/Interfaces/Faett_Channel_Serializer_Interfaces_Serializer#serialize()
     */
    public function serialize()
    {
        // initialize a new DOM document
        $doc = new DOMDocument('1.0', 'UTF-8');
        // create new namespaced root element
        $p = $doc->createElementNS(
        	$this->_namespace,
        	'p'
        );
        // add the schema to the root element
        $p->setAttributeNS(
        	'http://www.w3.org/2001/XMLSchema-instance',
        	'xsi:schemaLocation',
        	'http://pear.php.net/dtd/rest.package http://pear.php.net/dtd/rest.package.xsd'
        );
        // return the DOMDocuments XML representation
        return $this->_content($doc, $p)->saveXML();
    }

    /**
	 * Simple representation without namespaces, for
	 * importing into other ressources.
	 *
	 * @return string The
     */
    public function simple()
    {
        // initialize a new DOM document
        $doc = new DOMDocument('1.0', 'UTF-8');
        // create new simple root element
        $p = $doc->createElement('p');
        // attach the body
        $this->_content($doc, $p);
        // return the node
        return $p;
    }

    /**
     * Attaches the content to the passed root node, appends it to the
     * also passed DOMDocument and returns the DOMDocument itself.
     *
     * @param DOMDocument $doc
     * @param DOMElement $p
     * @return unknown
     */
    protected function _content(DOMDocument $doc, DOMElement $p)
    {
        try {
            // create an element for the package's name
            $n = $doc->createElement('n');
            $n->nodeValue = $this->_package->getPackageName();
            $p->appendChild($n);
            // create an element for the packages's channel name
            $c = $doc->createElement('c');
            $c->nodeValue = Mage::helper('channel')->getChannelName();
            $p->appendChild($c);
            // create an element for the link to the package's category directory
            $packageCategory = $this->_package->getAttributeText('package_category');
            $ca = $doc->createElement('ca');
            $ca->setAttributeNS(
            	'http://www.w3.org/1999/xlink',
            	'xlink:href',
            	'/channel/index/c/'.$packageCategory
            );
            $ca->nodeValue = $packageCategory;
            $p->appendChild($ca);
            // create an element for the package's licence
            $l = $doc->createElement('l');
            $l->nodeValue = $this->_package->getLicence();
            $p->appendChild($l);
            // create an element for the package's licence url
            $lu = $doc->createElement('lu');
            $lu->nodeValue = $this->_package->getLicenceUri();
            $p->appendChild($lu);
            // create an element for the package's summary
            $s = $doc->createElement('s');
            $s->nodeValue = $this->_package->getShortDescription();
            $p->appendChild($s);
            // create an element for the package's description
            $d = $doc->createElement('d');
            $d->nodeValue = $this->_package->getDescription();
            $p->appendChild($d);
            // create an element for the link to the package's release directory
            $r = $doc->createElement('r');
            $r->setAttributeNS(
            	'http://www.w3.org/1999/xlink',
            	'xlink:href',
            	'/channel/index/r/'.$this->_package->getPackageName()
            );
            $p->appendChild($r);
            // check if the package is deprecated
            if ($this->_package->getDeprecated()) {
                // create an element for the new package's channel
                $dc = $doc->createElement('dc');
                $dc->nodeValue = $this->_package->getDeprecatedChannel();
                $p->appendChild($dc);
                // create an element for the new package's name
                $dp = $doc->createElement('dp');
                $dp->nodeValue = $this->_package->getDeprecatedPackage();
                $p->appendChild($dp);
            }
            // append the root element to the DOM tree
            $doc->appendChild($p);
            // return the DOMDocument
            return $doc;
        } catch(Exception $e) {
            return $e->getMessage();
        }
    }
}