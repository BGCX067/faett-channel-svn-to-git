<?php

/**
 * Faett_Channel_Serializer_Category_PackagesInfo
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
class Faett_Channel_Serializer_Category_PackagesInfo
    extends Faett_Channel_Serializer_Category_Abstract {

    /**
     * Passes the category the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Category $package
     * 		The category to serializer has to be attached to
     * @return void
     */
    public function __construct(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Category $category) {
        // set the passed category
        Faett_Channel_Serializer_Category_Abstract::__construct(
            $user,
            $category
        );
        // set the namespace
        $this->setNamespace(
            Faett_Channel_Serializer_Category_Abstract::REST_CATEGORYPACKAGES
        );
    }

    /**
     * Returns the collection with the category's products.
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     * 		The collection with the category's products
     */
    protected function _getCollection()
    {
    	// load the channel
    	$channel = Mage::getModel('channel/channel')
    		->load(Mage::app()->getStore()->getId());
    	// return the Collection with the channel's packages, filtered by the package category
		return Mage::helper('channel')
			->getPackageCollection($channel)
			->addFieldToFilter('package_category', $this->_category->getId());
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
            $f = $doc->createElementNS(
            	$this->_namespace,
            	'f'
            );
            // add the schema to the root element
            $f->setAttributeNS(
            	'http://www.w3.org/2001/XMLSchema-instance',
            	'xsi:schemaLocation',
            	'http://pear.php.net/dtd/rest.categorypackages http://pear.php.net/dtd/rest.categorypackages.xsd'
            );
            // create an element for the categories's package
            $pi = $doc->createElement('pi');
            // attach the packages information
            foreach ($this->_getCollection() as $package) {
                // create a node to import packages /p/<name>/info.xml information
                $pi->appendChild(
                    $doc->importNode($this->_package($package->getId()), true)
                );
                // check if releases are available
                $hasLinks = $package->getTypeInstance(true)->hasLinks(
                    $package
                );
                // if releases are available
                if ($hasLinks) {
                    // load the releases
                    $links = $package->getTypeInstance(true)->getLinks(
                        $package
                    );
                    // create a new node for the releases
                    $a = $doc->createElement('a');
                    // iterate over the channel's releases
                    foreach ($links as $link) {
                        // add a new node for every release
                        $r = $doc->createElement('r');
                        // add the releae's version
                        $v = $doc->createElement('v');
                        $v->nodeValue = $link->getVersion();
                        $r->appendChild($v);
                        // add the releae's stability
                        $s = $doc->createElement('s');
                        $s->nodeValue = $link->getState();
                        $r->appendChild($s);
                        $a->appendChild($r);
                    }
                    // append the release
                    $pi->appendChild($a);
                    // iterate over the channel's releases
                    foreach ($links as $link) {
                        // add a new node for every dependency
                        $deps = $doc->createElement('deps');
                        // add the dependency's version
                        $v = $doc->createElement('v');
                        $v->nodeValue = $link->getVersion();
                        $deps->appendChild($v);
                        // add the release's dependeny as serialized array
                        $d = $doc->createElement('d');
                        $d->nodeValue = $link->getDependencies();
                        $deps->appendChild($d);
                        $pi->appendChild($deps);
                    }
                }
            }
            // append the element with the channel's name to the root element
            $f->appendChild($pi);
            // append the root element to the DOM tree
            $doc->appendChild($f);
            // return the XML document
            return $doc->saveXML();
        } catch(Exception $e) {
            return $e->getMessage();
        }
    }

    /**
	 * Loads a simple version of the package information for
	 * importing it into the packages info.
	 *
	 * @param integer $id The package id
	 * @return DOMElement The element to import
     */
    protected function _package($id)
    {
        // load the package model
		$package = Mage::getModel(Faett_Channel_Model_Package::MODEL);
        // try to load the if of the requested package
	    $package->load($id);
        // loading a simple version of the package info
	    return Mage::getModel('channel/serializer_package')->simple(
		    $this->getUser(),
		    $package
	    );
    }
}