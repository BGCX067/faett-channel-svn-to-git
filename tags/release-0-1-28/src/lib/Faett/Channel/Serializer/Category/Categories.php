<?php

/**
 * Faett_Channel_Serializer_Category_Categories
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
class Faett_Channel_Serializer_Category_Categories
    extends Faett_Channel_Serializer_Category_Abstract {

    /**
     * Passes the category the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Category $category
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
            Faett_Channel_Serializer_Category_Abstract::REST_ALLCATEGORIES
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
            	'http://pear.php.net/dtd/rest.allcategories http://pear.php.net/dtd/rest.allcategories.xsd'
            );
            // create an element for the channel's name
            $ch = $doc->createElement('ch');
            $ch->nodeValue = Mage::helper('channel')->getChannelName();
            // append the element with the channel's name to the root element
            $a->appendChild($ch);
            // load the product's attributes
            $attributes = $this->_category->getSelectOptions();
            // iterate over the channel's categories
            foreach ($attributes as $attribute) {
                $c = $doc->createElement('c');
                $c->setAttributeNS(
                	'http://www.w3.org/1999/xlink',
                	'xlink:href',
                	'/channel/index/c/'.$attribute.'/info.xml'
                );
                $c->nodeValue = $attribute;
                // append the XLink to the root element
                $a->appendChild($c);
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