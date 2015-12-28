<?php

/**
 * TechDivison_Licenceserver_Model_Package_Api
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
 * Catalog product api.
 * 
 * @category   	Faett
 * @package    	Faett_Channel
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Channel_Model_Package_Api
    extends Mage_Catalog_Model_Api_Resource {

    /**
     * Return loaded product instance
     *
     * @param  int|string $productId (SKU or ID)
     * @param  int|string $store
     * @return Mage_Catalog_Model_Product
     */
    protected function _getPackageByPackageName($packageName, $store = null)
    {
        // load the package
        $package = Mage::getModel('channel/package');
        if ($store !== null) {
            // attach the store id if available
            $package->setStoreId($this->_getStoreId($store));
        }
        // return the initialized package
        return $package->loadByPackageName($packageName);
    }

    /**
     * Validates the licence passed from the customer's shop.
     *
     * @param unknown_type $licence
     * @param unknown_type $store
     * @param unknown_type $attributes
     * @return boolean TRUE if the passed serialz is valid, else FALSE
     */
    public function validate($serialz, $websiteCode, $attributes = null)
    {
        // load the requested package
        $package = $this->_getPackageByPackageName(
            $attributes['packageName']
        );
        // load the website and the user
        $websiteId = Mage::getModel('core/website')->load($websiteCode, 'code')->getId();
        $user = $this->_getSession()->getUser();
        // load the customer
        $customer = Mage::getModel('customer/customer');
        if (!empty($websiteId)) {
        	$customer->setWebsiteId($websiteId);
        } else {
        	$customer->setWebsiteId(1);
        }
        
        $customer->loadByEmail($user->getEmail());
        // load the customers purchased links
        $collection = Mage::getModel('package/link_purchased')->getCollection()
            ->addFieldToFilter('customer_id', $customer->getId())
            ->addFieldToFilter('product_id', $package->getId())
            ->addFieldToFilter('serialz', $serialz);
            
        Mage::log(
        	'Found ' . sizeof($collection) . ' purchases for ' . 
        	'customer ' . $customer->getId() . ' ' .
        	'product ' . $package->getId() . ' ' .
        	'serialz ' . $serialz
        );
            
        // check the serialz
        foreach ($collection as $linkPurchased) {
    	
        	Mage::log(var_export($linkPurchased, true));
        
            if( Mage::getModel('channel/serialz')
                ->init($linkPurchased)
                ->isValid($serialz)) {
                return true;
            }
        }
        // return false if no valid serialz was found
        return false;
    }

    /**
     * Retrieve package info by the passed package name
     *
     * @param string $packageName The package name of the requested package
     * @param string|int $store
     * @param stdClass $attributes
     * @return array
     */
    public function info($packageName, $websiteCode = null, $attributes = null)
    {

        $package = $this->_getPackageByPackageName($packageName);

        if (!$package->getId()) {
            $this->_fault('not_exists');
        }

        $result = array( // Basic product data
            'product_id'   => $package->getId(),
            'package_name' => $package->getPackageName(),
            'sku'          => $package->getSku(),
            'set'          => $package->getAttributeSetId(),
            'type'         => $package->getTypeId(),
            'categories'   => $package->getCategoryIds(),
            'websites'     => $package->getWebsiteIds(),
        );

        $result['image'] =  Mage::helper('catalog/image')->init(
            $package, 'image'
        )->__toString();

        $allAttributes = array();
        if (isset($attributes->attributes)) {
            $allAttributes += array_merge($allAttributes, $attributes->attributes);
        }

        $_additionalAttributeCodes = array();
        if (isset($attributes->additional_attributes)) {
            foreach ($attributes->additional_attributes as $k => $_attributeCode) {
                $allAttributes[] = $_attributeCode;
                $_additionalAttributeCodes[] = $_attributeCode;
            }
        }

        $_additionalAttribute = 0;
        foreach ($package->getTypeInstance(true)->getEditableAttributes($package) as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $allAttributes)) {
                if (in_array($attribute->getAttributeCode(), $_additionalAttributeCodes)) {
                    $result['additional_attributes'][$_additionalAttribute]['key'] = $attribute->getAttributeCode();
                    $result['additional_attributes'][$_additionalAttribute]['value'] = $package->getData($attribute->getAttributeCode());
                    $_additionalAttribute++;
                } else {
                    $result[$attribute->getAttributeCode()] = $package->getData($attribute->getAttributeCode());
                }
            }
        }

        $result['links'] = $this->_loadAvailableLinks($package, $websiteCode);

        return $result;
    }

    protected function _loadAvailableLinks(Faett_Channel_Model_Package $package, $store = null)
    {
        // load the customers purchases
        $links = Mage::getResourceModel('package/link_collection')
            ->addFieldToFilter('product_id', $package->getId())
            ->addOrder('version', 'desc');
        // load the customers purchases
        $purchasedLinks = $this->_loadPurchasedLinks($package, $store);
        // initialize the array for all available purchases
        $result = array();
        // assemble the data
        foreach ($links as $linkId => $link) {
            // convert the data into an array
            $l = $link->toArray(
                array(
                    'version',
                    'state',
                    'licence',
                    'release_date',
                    'package_name',
                    'licence_uri',
                    'state'
                )
            );
            // merge the array with the customers purchases
            if (array_key_exists($linkId, $purchasedLinks)) {
                $l = array_merge($l, $purchasedLinks[$linkId]);
            }
            // attach them to the result
            $result[] = $l;
        }
        // return the result
        return $result;
    }

    protected function _loadPurchasedLinks(Faett_Channel_Model_Package $package, $websiteCode = null)
    {
        // load the website and the user logged into the system
        $websiteId = Mage::getModel('core/website')
            ->load($websiteCode, 'code')->getId();
        $user = $this->_getSession()->getUser();
        // load the customer
        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId($websiteId)->loadByEmail($user->getEmail());
        // load the customers purchases
        $purchased = Mage::getResourceModel('package/link_purchased_collection')
            ->addFieldToFilter('customer_id', $customer->getId())
            ->addOrder('created_at', 'desc');
        // intialize the array for the purchased links
        $result = array();
        // add the releases of the purchases
        foreach ($purchased as $id => $linkPurchased) {
            $result[$id] = $linkPurchased->load($id)->toArray();
        }
        // return the result
        return $result;
    }
}