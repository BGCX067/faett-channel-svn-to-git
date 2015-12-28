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
class Faett_Channel_Serializer_Package_Api
    extends Faett_Channel_Serializer_Release_Abstract {

    /**
     * The channel itself.
     * @var Faett_Channel_Model_Channel
     */
    protected $_channel = null;

    /**
     * Passes the package the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Package $package
     * 		The package to serializer has to be attached to
     * @param Faett_Channel_Model_Channel $channel
     * 		The channel itself
     * @return void
     */
    public function __construct(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Package $package,
    	Faett_Channel_Model_Channel $channel) {
    	// call the parent constructor
        parent::__construct($user, $package);
    	// set the channel itself
        $this->_channel = $channel;
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

    protected function _resources($doc, $api)
    {
        $resources = $doc->createElement('resources');
        
            $channel = $doc->createElement($this->getChannel()->getCode());
            
            	$methods = $doc->createElement('methods');

                	$package = $doc->createElement($name = strtolower($this->_package->getPackageName()));
                	$package->setAttribute('translate', 'title');
                	$package->setAttribute('module', 'channel');
                	
                    	$title = $doc->createElement('title');
                    	$title->nodeValue = $this->_package->getPackageName();
                    	
                    	$acl = $doc->createElement('acl');
                    	$acl->nodeValue = 'channel/' . $this->getChannel()->getCode() . '/' . $name;
                    	
                 	$package->appendChild($acl);
               		$package->appendChild($title);

            	$methods->appendChild($package);
                        
            $channel->appendChild($methods);
            
        $resources->appendChild($channel);

        $api->appendChild($resources);
    }

    protected function _acl($doc, $api)
    {
        $acl = $doc->createElement('acl');
        
            $resources = $doc->createElement('resources');
            
            	$chn = $doc->createElement('channel');
            
	                $channel = $doc->createElement($this->getChannel()->getCode());
	                
	                	$package = $doc->createElement(strtolower($this->_package->getPackageName()));
	                	$package->setAttribute('translate', 'title');
	                    $package->setAttribute('module', 'channel');
	                            
	                    	$title = $doc->createElement('title');
	                    	$title->nodeValue = $this->_package->getPackageName();
	
	                	$package->appendChild($title);
	                        
	                $channel->appendChild($package);
                
                $chn->appendChild($channel);
                
            $resources->appendChild($chn);
            
        $acl->appendChild($resources);

        $api->appendChild($acl);
    }
}