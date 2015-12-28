<?php

/**
 * Faett_Channel_Serializer_Package_Packages
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
class Faett_Channel_Serializer_Package_Packages
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
            Faett_Channel_Serializer_Package_Abstract::REST_ALLPACKAGES
        );
    }

    /**
     * Returns the collection with the channels products.
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     * 		The collection with the products
     */
    protected function _getCollection()
    {
    	// load the channel
    	$channel = Mage::getModel('channel/channel')
    		->load(Mage::app()->getStore()->getId());
    	// return the collection with the channel's packages
		return Mage::helper('channel')
			->getPackageCollection($channel);
    	
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
            	'http://pear.php.net/dtd/rest.allpackages http://pear.php.net/dtd/rest.allpackages.xsd'
            );
            // create an element for the channel's name
            $c = $doc->createElement('c');
            $c->nodeValue = Mage::helper('channel')->getChannelName();
            // append the element with the channel's name to the root element
            $a->appendChild($c);
            // iterate over the channel's products
            foreach ($this->_getCollection() as $id => $package) {
                // load the package
                $package->load($id);
                // and check the type
                if ($package->getTypeInstance(true) instanceof Faett_Package_Model_Product_Type) {
                    // create element, load and set the product
                    $p = $doc->createElement('p');
                    $p->nodeValue = $package->getPackageName();
                    // append the element to the root element
                    $a->appendChild($p);
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