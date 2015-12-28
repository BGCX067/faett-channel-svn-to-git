<?php

/**
 * Faett_Channel_Serializer_Maintainer_AllMaintainers
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
class Faett_Channel_Serializer_Maintainer_AllMaintainers
    extends Faett_Channel_Serializer_Maintainer_Abstract {

    /**
     * Passes the maintainer the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Maintainer $maintainer
     * 		The maintainer to serializer has to be attached to
     * @return void
     */
    public function __construct(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Maintainer $maintainer) {
        // set the passed package
        Faett_Channel_Serializer_Maintainer_Abstract::__construct(
            $user,
            $maintainer
        );
        // set the namespace
        $this->setNamespace(
            Faett_Channel_Serializer_Maintainer_Abstract::REST_ALLMAINTAINERS
        );
    }

    /**
     * Returns a collection with all API users.
     *
     * @return Mage_Api_Model_Mysql4_User_Collection The API user collection
     */
    public function _getCollection()
    {
        return Mage::getResourceModel('api/user_collection');
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
            	'http://pear.php.net/dtd/rest.allmaintainers http://pear.php.net/dtd/rest.allmaintainers.xsd'
            );
            // iterate over the channel's categories
            foreach ($this->_getCollection() as $id => $maintainer) {
                // load the maintainer itself
                $handle = $maintainer->load($id)->getUsername();
                // check if a handle is set
                if (!empty($handle)) {
                    // create the element with the link to the maintainer
                    $h = $doc->createElement('h');
                    $h->setAttributeNS(
                    	'http://www.w3.org/1999/xlink',
                    	'xlink:href',
                    	Mage::helper(
                    		'channel'
                    	)->getRESTUrl().'m'.DS.$handle
                    );
                    // set the maintainer handle
                    $h->nodeValue = $handle;
                    // append the XLink to the root element
                    $m->appendChild($h);
                }
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