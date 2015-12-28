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
 * This model extends the Magento Product by adding some new attributes to it.
 * The new attributes are defined as followed:
 *
 * <ul>
 *   <li>string licence The package's licence</li>
 *   <li>string licence_uri The URI of the licence</li>
 *   <li>boolean deprecated TRUE if the package is deprecated, else FALSE</li>
 *   <li>string deprecated_channel The channel for the replacement package</li>
 *   <li>string deprecated_package The name of the replacement package</li>
 * </ul>
 *
 * @category   Faett
 * @package    Faett_Channel
 * @copyright  Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    <http://www.gnu.org/licenses/> 
 * 			   GNU General Public License (GPL 3)
 * @author     Tim Wagner <tw@faett.net>
 */
class Faett_Channel_Model_Package
    extends Mage_Catalog_Model_Product {

    const MODEL = 'channel/package';
    
    const REGISTRY = 'package';

    private $_serializer = null;

    /**
     * Flag to identify a newly created package.
     * @var boolean
     */
    protected $_isNew = false;

    /**
     * Invoked by parent class, before the
     * Package is saved.
     *
     * @return void
     */
    protected function _beforeSave()
    {
    	// invoke the parent method
    	parent::_beforeSave();
        // check the product type (has to be a package product)
        $typeInstance = $this->getTypeInstance(true);
        // check if a ID is available, if not the package is new
        if ($this->getId() == 0 &&
            $typeInstance instanceof Faett_Package_Model_Product_Type) {
            $this->_isNew = true;
            $this->setHasOptions(1);
        }
    }

    /**
     * Invoked by parent class, after the
     * Package was saved.
     *
     * @return void
     */
    protected function _afterSave()
    {
        // invoke parent method
        parent::_afterSave();
        // check if package is new
        if ($this->_isNew == true) {
            // initialize the options
            $options = 	array(
    			'price'		    => 0,
    			'price_type'	=> 'fixed'
    		);
            // load the available subscription information
    		$subscriptionTypes = Mage::getModel('channel/subscription_type')
    		    ->getCollection();
            // create the custom options
            foreach ($subscriptionTypes as $subscription) {
                // initialize the array for the custom options
                $values = array();
                // set the default values
                $options['is_require'] = $subscription->getIsRequired();
                $options['type'] = $subscription->getType();
                // add the option information
                foreach ($subscription->getOptions() as $option) {
                    $values[] = array(
        				'title'		    => $option->getTitle(),
        				'price'		    => $option->getPrice(),
        				'price_type'	=> $option->getPriceType(),
        			    'sku'			=> $option->getSku(),
                        'sort_order'	=> $option->getSortOrder()
        			);
                }
                // prepare the custom option
            	$data = Mage::helper('channel')
                    ->setCustomOption(
                        $this->getId(),
                        $subscription->getTitle(),
                        $options,
                        $values
                    );
                // attache the custom option to the package
        		$option = Mage::getModel('catalog/product_option')
        		    ->setData($data)
        		    ->setProduct($this)
        		    ->save();
            }
        }
    }

    public function getApiConfig(Faett_Channel_Model_Channel $channel)
    {
        $serializer = new Faett_Channel_Serializer_Package_Api(
            Mage::getModel('api/user'),
            $this,
            $channel
        );

        return $serializer->serialize();
    }

    public function setVersion($version)
    {
        $this->setData('version', $version);
    }

    public function getVersion()
    {
        return $this->getData('version');
    }

    /**
     * Loads the package with the passed name and returns
     * the initialized instance.
     *
     * @param string $packageName The unique package name to return
     * @return Faett_Channel_Model_Package The initialized package
     */
    public function loadByPackageName($packageName)
    {
        $collection = $this->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('package_name', $packageName)
            ->load()
            ->getItems();
        if (sizeof($collection)) {
            reset($collection);
            $package = current($collection);
            $this->setData($package->getData());
        }
        return $this;
    }

    /**
     * Returns the package maintainers with their
     * handle and the active flag set.
     *
     * @return Faett_Package_Model_Package_Maintainer_Collection
     * 		The package maintainers
     */
    public function getMaintainers()
    {
        // load the collection with the package maintainers
        $collection = Mage::getModel('package/package_maintainer')
            ->getCollection()
            ->addFieldToFilter('product_id', $this->getId())
            ->load();
        // attach the maintainers handle
        foreach ($collection as $relation) {
            $maintainer = Mage::getModel('api/user')->load($relation->getUserId());
            $relation->setUsername($maintainer->getUsername());
        }
        // return the collection
        return $collection;
    }

    /**
     * Sets the serialize to use for XML transformation.
     * 
     * @param Faett_Channel_Serializer_Interfaces_Serializer $serializer
     * @return Faett_Channel_Model_Package The instance
     */
    public function setSerializer(
        Faett_Channel_Serializer_Interfaces_Serializer $serializer) {
        $this->_serializer = $serializer;
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see lib/Varien/Varien_Object#__toXml($arrAttributes, $rootName, $addOpenTag, $addCdata)
     */
    public function __toXml(
        array $arrAttributes = array(),
        $rootName = 'a',
        $addOpenTag = false,
        $addCdata = true) {
        return $this->_serializer->serialize();
    }
}