<?php

/**
 * Faett_Channel_Model_Channel
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
class Faett_Channel_Model_Channel
    extends Mage_Core_Model_Store {

    /**
     * The model alias.
     * @var string
     */
    const MODEL = 'channel/channel';
    
    /**
     * The registry key for the block data.
     * @var string
     */
    const REGISTRY = 'channel';
    
    /**
     * The block model's cache tag.
     * @var string
     */
    const CACHE_TAG = 'channel';
    
    /**
     * The model's cache tag.
     * @var string
     */
    protected $_cacheTag = 'channel';

    /**
     * The REST versions the channel supports.
     * @var array
     */
    private $_restVersions = array('REST1.0', 'REST1.1');
    
    /**
     * (non-PHPdoc)
     * @see lib/Varien/Varien_Object#__toXml($arrAttributes, $rootName, $addOpenTag, $addCdata)
     */
    public function __toXml(
        array $arrAttributes = array(),
        $rootName = 'channel',
        $addOpenTag = false,
        $addCdata = true) {
        // initialize a new DOM document
        $doc = new DOMDocument('1.0', 'UTF-8');
        // create new root element
        $channel = $doc->createElement($rootName);
        // set the channel's version
        $channel->setAttribute('version', '1.0');
        // add the channel's name
        $name = $doc->createElement('name');
        $name->nodeValue = Mage::helper('channel')->getChannelName();
        $channel->appendChild($name);
        // load the store's ID
        $storeId = Mage::app()->getStore()->getId();
        // add the channel's summary
        $summary = $doc->createElement('summary');
        $summary->nodeValue = (string) Mage::getStoreConfig(
        	'channel/global/summary',
        	$storeId
        );
        $channel->appendChild($summary);
        // add the channel's alias
        $suggestedalias = $doc->createElement('suggestedalias');
        $suggestedalias->nodeValue = (string) Mage::getStoreConfig(
        	'channel/global/alias',
        	$storeId
        );
        $channel->appendChild($suggestedalias);
        // create the node for the REST url's
        $rest = $doc->createElement('rest');
        // append the REST url's
        for ($i = 0; $i < sizeof($this->_restVersions); $i++) {
            $baseurl = $doc->createElement('baseurl');
            $baseurl->setAttribute('type', $this->_restVersions[$i]);
            $baseurl->nodeValue = Mage::helper('channel')->getRESTUrl();
            $rest->appendChild($baseurl);
        }
        // add the node for the primary channel servers
        $primary = $doc->createElement('primary');
        $primary->appendChild($rest);
        // add the node for the channel servers
        $servers = $doc->createElement('servers');
        $servers->appendChild($primary);
        // add the node with the channel servers to the channel itself
        $channel->appendChild($servers);
        // append the root element to the DOM tree
        $doc->appendChild($channel);
        // return the XML document
        return $doc->saveXML();
    }
    
    /**
     * Returns TRUE if this is an active channel, else FALSE.
     * 
     * @return boolean
     * 		TRUE if this is an active channel
     */
    public function isChannel()
    {
    	return Mage::getStoreConfig('channel/global/is_channel', $this);
    }
    
    /**
     * Returns TRUE if the channel has authentication 
     * activated, else FALSE.
     * 
     * @return boolean
     * 		TRUE if channel authentication is activated
     */
    public function hasAuthentication()
    {
    	return Mage::getStoreConfig('channel/global/authentication', $this);
    }

    /**
     * Returns the XML configuration necessary for rendering
     * the ACL's.
     * 
     * @return string
     * 		The channels ACL configuration
     */
    public function getApiConfig()
    {
    	// initialize the serializer
        $serializer = new Faett_Channel_Serializer_Channel_Api(
            Mage::getModel('api/user'),
            $this
        );
		// serialize the XML structure and return it
        return $serializer->serialize();
    }
}