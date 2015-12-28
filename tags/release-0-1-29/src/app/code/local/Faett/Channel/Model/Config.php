<?php

/**
 * Faett_Channel_Model_Package
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
class Faett_Channel_Model_Config extends Mage_Api_Model_Config {

    /**
     * Init configuration for webservices api
     *
     * @return Mage_Api_Model_Config
     */
    protected function _construct()
    {

        if (Mage::app()->useCache('config_api')) {
            if ($this->loadCache()) {
                return $this;
            }
        }

        $config = Mage::getConfig()->loadModulesConfiguration('api.xml');
        $config = $this->loadPackageConfiguration($config);

        $this->setXml($config->getNode('api'));

        if (Mage::app()->useCache('config_api')) {
            $this->saveCache();
        }

        return $this;
    }

    /**
     * Iterate all active modules "etc" folders and combine data from
     * specidied xml file name to one object
     *
     * @param   string $fileName
     * @param   null|Mage_Core_Model_Config_Base $mergeToObject
     * @return  Mage_Core_Model_Config_Base
     */
    public function loadPackageConfiguration(
        $mergeToObject = null,
        $mergeModel = null) {
		// initialize the configuration XML structure to append
        if ($mergeToObject === null) {
            $mergeToObject = new Mage_Core_Model_Config_Base();
            $mergeToObject->loadString('<config/>');
        }
		// initialize the configuration XML structure
        if ($mergeModel === null) {
            $mergeModel = new Mage_Core_Model_Config_Base();
        }        
        // load the Collection with all channels
        $channels = Mage::getModel('channel/channel')
            ->getCollection();
		// iterate over the channels
        foreach ($channels as $id => $channel) {
        	// if the store is an activated channel
        	if ($channel->load($id)->isChannel()) {
        		// merge the channel to the ACL's
	            if ($mergeModel->loadString($channel->getApiConfig())) {
	                $mergeToObject->extend($mergeModel, true);
	            }
        		// load the package Collection
			    $packages = Mage::helper('channel')->getPackageCollection($channel);
        		// merge the packages to the channel's ACL's
			    foreach ($packages as $id => $package) {
			    	if ($mergeModel->loadString($package->load($id)->getApiConfig($channel))) {
			        	$mergeToObject->extend($mergeModel, true);
			    	}
				}
        	}
        }
        // return the configuration structure
        return $mergeToObject;
    }

    /**
     * Load Acl resources from config
     *
     * @param Mage_Api_Model_Acl $acl
     * @param Mage_Core_Model_Config_Element $resource
     * @param string $parentName
     * @return Mage_Api_Model_Config
     */
    public function loadAclResources(Mage_Api_Model_Acl $acl, $resource=null, $parentName=null)
    {
        $resourceName = null;
        if (is_null($resource)) {
            $resource = $this->getNode('acl/resources');
        } else {
            $resourceName = (is_null($parentName) ? '' : $parentName.'/') . $resource->getName();
            $acl->add(Mage::getModel('api/acl_resource', $resourceName), $parentName);
        }

        $children = $resource->children();

        if (empty($children)) {
            return $this;
        }

        foreach ($children as $res) {
            if ($res->getName() != 'title' && $res->getName() != 'sort_order') {
                $this->loadAclResources($acl, $res, $resourceName);
            }
        }
        return $this;
    }
}