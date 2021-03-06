<?php

/**
 * Faett_Channel_Block_Package
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
 * Block to render XML structure for a package request.
 *
 * @category   	Faett
 * @package    	Faett_Channel
 * @copyright  	Copyright (c) 2011 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Channel_Block_Package 
	extends Mage_Core_Block_Template {
	
	/**
	 * The cache lifetime in seconds
	 * @var integer
	 */
	const CACHE_LIFETIME = 3600;
    
	/**
	 * (non-PHPdoc)
	 * @see Mage_Core_Block_Abstract::getCacheLifetime()
	 */	
    public function getCacheLifetime()
    {
    	// return the cache lifetime
    	return self::CACHE_LIFETIME;
    }
    
    /**
     * (non-PHPdoc)
     * @see Mage_Core_Block_Abstract::getCacheTags()
     */
    public function getCacheTags()
    {
    	// return the cache tags
    	return array(
        	Faett_Channel_Model_Package::CACHE_TAG,
        	Mage_Core_Model_Store::CACHE_TAG
       );
    }
    
    /**
     * (non-PHPdoc)
     * @see Mage_Core_Block_Abstract::getCacheKey()
     */
    public function getCacheKey()
    {	
    	// initialize the cache key to use and return it
    	return Faett_Channel_Model_Package::CACHE_TAG . 
    		'_' . $this->getPackage()->getId() .
    		'_' . Mage::app()->getStore()->getId() .
    		'_' . $this->getType();
    }
    
    /**
     * Returns the channel's package
     * set in the controller.
     * 
     * @return Faett_Channel_Model_Package
     * 		The channel package
     */
    public function getPackage()
    {
    	return Mage::registry(Faett_Channel_Model_Package::REGISTRY);
    }
    
    /**
     * Returns the requested resource type, e. g. 'version' if
     * p/faett_channel/maintainers.xml is requested.
     * 
     * @return string 
     * 		The requested resource type
     */
    public function getType()
    {
    	return Mage::registry(Faett_Channel_Helper_Data::TYPE);
    }

	/**
	 * (non-PHPdoc)
	 * @see Mage_Core_Block_Template::_toHtml()
	 */
    protected function _toHtml() 
    {
    	// return valid XML here
    	return $this->getPackage()->__toXML();
    }
}