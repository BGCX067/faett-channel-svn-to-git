<?php

/**
 * Faett_Channel_Serializer_Release_Package
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
class Faett_Channel_Serializer_Channel_Api
    extends Faett_Channel_Serializer_Abstract {

    /**
     * The channel itself.
     * @var Faett_Channel_Model_Channel
     */
    protected $_channel = null;

    /**
     * Array with the available namespaces for this ressource type
     * @var array
     */
    protected $_namespaces = array(
        Faett_Channel_Serializer_Package_Abstract::REST_PACKAGE,
        Faett_Channel_Serializer_Package_Abstract::REST_PACKAGEMAINTAINERS,
        Faett_Channel_Serializer_Package_Abstract::REST_ALLPACKAGES
    );
    	
    /**
     * Passes the user for validation purposes.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Channel $channel
     * 		The channel itself
     * @return void
     */
    public function __construct(
    	Mage_Api_Model_User $user, 
    	Faett_Channel_Model_Channel $channel) {
        // call the parent constructor
    	parent::__construct($user);
    	// set the channel itself
        $this->_channel = $channel;
    }
    
    /**
     * Returns the channel itself.
     * 
     * @return Faett_Channel_Model_Channel
     * 		The channel itself
     */
    public function getChannel()
    {
    	return $this->_channel;
    }

    /**
     * (non-PHPdoc)
     * @see lib/Faett/Channel/Serializer/Faett_Channel_Serializer_Abstract#_getNamespaces()
     */
    protected function _getNamespaces()
    {
        return $this->_namespaces;
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
            $config = $doc->createElement('config');
            // create a new API element
            $api = $doc->createElement('api');
            // set the resources and the ACL's
            $this->_resources($doc, $api);
            $this->_acl($doc, $api);
            // append the API node
            $config->appendChild($api);
            // append the config node
            $doc->appendChild($config);
            // return the XML document
            return $doc->saveXML();
        } catch(Exception $e) {
            Mage::logException($e);
            return $e->getMessage();
        }
    }

    protected function _resources($doc, $api)
    {
        $resources = $doc->createElement('resources');
        
            $channel = $doc->createElement($this->getChannel()->getCode());
            $channel->setAttribute('translate', 'title');
            $channel->setAttribute('module', 'channel');          
            
               	$channelTitle = $doc->createElement('title');
               	$channelTitle->nodeValue = $this->getChannel()->getName() . ' channel ACL\'s';
               
               	$channelModel = $doc->createElement('model');
               	$channelModel->nodeValue = 'channel/serializer_channel';
               
               	$channelAcl = $doc->createElement('acl');
               	$channelAcl->nodeValue = 'channel/' . $this->getChannel()->getCode();

               	$methods = $doc->createElement('methods');

               		$m = $doc->createElement('m');
                    $m->setAttribute('translate', 'title');
                    $m->setAttribute('module', 'channel');
                    	$titleInfo = $doc->createElement('title');
                    	$titleInfo->nodeValue = 'Retrieve maintainer data';
                        $acl = $doc->createElement('acl');
                        $acl->nodeValue = 'channel/' . $this->getChannel()->getCode() . '/m';
                    $m->appendChild($titleInfo);
                    $m->appendChild($acl);

               		$p = $doc->createElement('p');
                    $p->setAttribute('translate', 'title');
                    $p->setAttribute('module', 'channel');
                    	$titleInfo = $doc->createElement('title');
                    	$titleInfo->nodeValue = 'Retrieve package data';
                        $acl = $doc->createElement('acl');
                        $acl->nodeValue = 'channel/' . $this->getChannel()->getCode() . '/p';
                    $p->appendChild($titleInfo);
                    $p->appendChild($acl);

               		$r = $doc->createElement('r');
                    $r->setAttribute('translate', 'title');
                    $r->setAttribute('module', 'channel');
                    	$titleInfo = $doc->createElement('title');
                    	$titleInfo->nodeValue = 'Retrieve release data';
                        $acl = $doc->createElement('acl');
                        $acl->nodeValue = 'channel/' . $this->getChannel()->getCode() . '/r';
                    $r->appendChild($titleInfo);
                    $r->appendChild($acl);

               		$c = $doc->createElement('c');
                    $c->setAttribute('translate', 'title');
                    $c->setAttribute('module', 'channel');
                    	$titleInfo = $doc->createElement('title');
                    	$titleInfo->nodeValue = 'Retrieve category data';
                        $acl = $doc->createElement('acl');
                        $acl->nodeValue = 'channel/' . $this->getChannel()->getCode() . '/c';
                    $c->appendChild($titleInfo);
                    $c->appendChild($acl);

                $methods->appendChild($c);
                $methods->appendChild($r);
                $methods->appendChild($p);
                $methods->appendChild($m);
                        
        	$channel->appendChild($methods);
        	$channel->appendChild($channelAcl);
        	$channel->appendChild($channelModel);
        	$channel->appendChild($channelTitle);
                    
        $resources->appendChild($channel);

        $api->appendChild($resources);
    }

    protected function _acl($doc, $api)
    {
        $acl = $doc->createElement('acl');
        
            $resources = $doc->createElement('resources');
            
                $channel = $doc->createElement('channel');
                
                    $store = $doc->createElement($this->getChannel()->getCode());
                    $store->setAttribute('translate', 'title');
                    $store->setAttribute('module', 'channel');
                    
                    	$titleChannel = $doc->createElement('title');
                        $titleChannel->nodeValue = $this->getChannel()->getName() . ' ACL\'s';
                    
                        $m = $doc->createElement('m');
                        $m->setAttribute('translate', 'title');
                        $m->setAttribute('module', 'channel');
                        	$titleInfo = $doc->createElement('title');
                            $titleInfo->nodeValue = 'Maintainer abfragen';
                        $m->appendChild($titleInfo);     
                    
                        $p = $doc->createElement('p');
                        $p->setAttribute('translate', 'title');
                        $p->setAttribute('module', 'channel');
                        	$titleInfo = $doc->createElement('title');
                            $titleInfo->nodeValue = 'Packages abfragen';
                        $p->appendChild($titleInfo);       
                    
                        $r = $doc->createElement('r');
                        $r->setAttribute('translate', 'title');
                        $r->setAttribute('module', 'channel');
                        	$titleInfo = $doc->createElement('title');
                            $titleInfo->nodeValue = 'Releases abfragen';
                        $r->appendChild($titleInfo);   
                    
                        $c = $doc->createElement('c');
                        $c->setAttribute('translate', 'title');
                        $c->setAttribute('module', 'channel');
                        	$titleInfo = $doc->createElement('title');
                            $titleInfo->nodeValue = 'Categories abfragen';
                        $c->appendChild($titleInfo);
                        
                    $store->appendChild($c);
                    $store->appendChild($r);
                    $store->appendChild($p);
                    $store->appendChild($m);
					$store->appendChild($titleChannel);
					
                $channel->appendChild($store);
                
            $resources->appendChild($channel);
            
        $acl->appendChild($resources);

        $api->appendChild($acl);
    }
}