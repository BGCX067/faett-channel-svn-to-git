<?php

/**
 * Faett_Channel_Serializer_Category_Packages
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
class Faett_Channel_Serializer_Category_Packages
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
            $l = $doc->createElementNS(
            	$this->_namespace,
            	'l'
            );
            // add the schema to the root element
            $l->setAttributeNS(
            	'http://www.w3.org/2001/XMLSchema-instance',
            	'xsi:schemaLocation',
            	'http://pear.php.net/dtd/rest.categorypackages http://pear.php.net/dtd/rest.categorypackages.xsd'
            );
            // attach the packages information
            foreach ($this->_getCollection() as $id => $package) {
                // load the package
                $package->load($id);
                // create the element with the link to the package
                $p = $doc->createElement('p');
                $p->setAttributeNS(
                	'http://www.w3.org/1999/xlink',
                	'xlink:href',
                	'/channel/index/p/'.$packageName = $package->getPackageName()
                );
                // set the maintainer handle
                $p->nodeValue = $packageName;
                // append the XLink to the root element
                $l->appendChild($p);
            }
            // append the root element to the DOM tree
            $doc->appendChild($l);
            // return the XML document
            return $doc->saveXML();
        } catch(Exception $e) {
            return $e->getMessage();
        }
    }
}