<?php

/**
 * Faett_Channel_Model_Serialz
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
class Faett_Channel_Model_Serialz extends Mage_Core_Model_Abstract
{

    /**
     * A number used to fill up the customer and product ID.
     * @var integer
     */
    protected $_number = 100000;

    /**
     * The Magento Encryptor instance to use for encryption
     * @var TechDivision_PasswordAES_Model_Encryptor
     */
    protected $_encryptor = null;

    /**
     * Components contained by the serialz.
     * @var array
     */
    protected $_components = array(
        '_customerId' => 6,
        '_productId'  => 6,
        '_validFrom'  => 8,
        '_validThru'  => 8
    );

    protected $_dateFormat = array(
        'y' => 4,
        'm' => 2,
        'd' => 2
    );

    /**
     * Date up from when the serialz is valid in format 'YYYYmmdd'.
     * @var integer
     */
    protected $_validFrom = 0;

    /**
     * Date until the serialz is valid in format 'YYYYmmdd'.
     * @var integer
     */
    protected $_validThru = 0;

    /**
     * Customer ID.
     * @var integer
     */
    protected $_customerId = 0;

    /**
     * Product ID.
     * @var integer
     */
    protected $_productId = 0;

    /**
     * The purchased link.
     * @var Faett_Package_Model_Link_Purchased
     */
    protected $_linkPurchased = null;

    /**
     * Initialize the class with the purchased link and
     * the purchased link item.
     *
     * @param Faett_Package_Model_Link_Purchased $linkPurchased
     * 		The purchased link
     * @return Faett_Package_Model_Serialz The instance itself
     */
    public function init(
        Faett_Package_Model_Link_Purchased $linkPurchased) {
        // initialize the purchased link and the purchased link item
        $this->_linkPurchased = $linkPurchased;
        // load the encryption instance
        $this->_encryptor = Mage::helper('core')->getEncryptor();
        // return the instance itself
        return $this;
    }

    /**
	 * Create and return a hash for the download link.
	 *
	 * @return string The requested hash
     */
    public function serialz()
    {
        // load the order item and the selectd package options
        $orderItem = $this->_linkPurchased->getOrderItem();
        $productOptions = $orderItem->getProductOptions();
        // initialize the subscription runtime
        $now = Zend_Date::now();
        $then = Zend_Date::now()->addMonth(1);
        // check if custom options are available
        if (!array_key_exists('options', $productOptions)) {
        	$then->addMonth($this->_getDefaultSubscription());
        } else {      
	        // iterate over the package's custom options
	        foreach ($productOptions['options'] as $value) {
	            // load the select option itself
	            $option = Mage::getModel('catalog/product_option')
	                ->load($value['option_id']);
	            // load the selected option value
	            $value = $option->getValueInstance()
	                ->load($value['option_value']);
	            // initialize the subscription model
	            $subscription = Mage::getModel(
	            	'channel/subscription_type_option'
	           	);
	            // try to load the subscription
	            $subscription->loadBySku($value->getSku());
	            // check if a valid subscription was found
	            if ($subscription->getId() > 0) {
		            // if a subscription was found, use the subscription runtime
		            $then->addMonth($subscription->getValue() - 1);
	            } else {
	            	// if not, set the default subscription
	            	$then->addMonth($this->_getDefaultSubscription());
	            }
	        }
        }
        // intialize the serialz keys
        $components['_customerId'] = $this->_number + $this->_getCustomerId();
        $components['_productId'] = $this->_number + $this->_getProductId();
        $components['_validFrom'] = $now->toString('yyyyMMdd');
        $components['_validThru'] = $then->toString('yyyyMMdd');
        // initialize and encrypt the serialz
        $serialCrypted = $this->_encryptor->encrypt(
            implode('', $components)
        );
        // make a more 'look-a-like' serialz ;)
        for ($i = 0; $i < 64; $i = $i + 8) {
            $parts[] = strtoupper(
                substr($serialCrypted, $i, 8)
            );
        }
        // return the serialz
        return $serialz = implode("-", $parts);
    }
    
    /**
	 * Return the default subscription value.
	 * 
	 * @return integer The default subscription time in month
     */
    protected function _getDefaultSubscription()
    {
        // initialize the subscription model
        $subscription = Mage::getModel(
            'channel/subscription_type_option'
        );
    	// try to load the 'endless' subscription
        $subscription->loadBySku(
        	Faett_Channel_Model_Subscription_Type_Option::SKU_SUBSCRIPTION_TYPE_ENDLESS
        );
        // return the default value
    	return $subscription->getValue();
    }

    /**
     * This method decrypts the passed serialz and sets the
     * components of the serialz.
     *
     * @param string $serialz The serialz to decrypt
     * @return Faett_Channel_Model_Serialz
     */
    public function decrypt($serialz)
    {
        // convert the string to upper case
        $toDecrypt = strtolower(
            // remove the separators '-' from the serialz
            implode('', explode("-", $serialz))
        );
        // rebuild the original AES encrypted serialz
        $serialDecrypted = $this->_encryptor->decrypt($toDecrypt);
        // tokenize the uncrypted serialz into it's components
        $start = 0;
        foreach ($this->_components as $key => $length) {
            $this->$key = substr(
                $serialDecrypted,
                $start,
                $length
            );
            // raise the start position
            $start += $length;
        }
        // return the instance itself
        return $this;
    }

    /**
     * Return the date in ISO format up from
     * where the serialz is valid.
     *
     * @return string Valid from date
     */
    public function getValidFrom()
    {
        return $this->_toIsoDate($this->_validFrom);
    }

    /**
     * Return the date in ISO format up to when
     * the serialz is valid.
     *
     * @return string Valid to date
     */
    public function getValidThru()
    {
        return $this->_toIsoDate($this->_validThru);
    }

    /**
     * Returns the date string in format yyyyMMdd
     * as valid ISO date in format yyyy-MM-dd.
     *
     * @param integer $from The date to convert
     * @return string The converted date
     */
    protected function _toIsoDate($from)
    {
        $start = 0;
        foreach($this->_dateFormat as $part => $length) {
            $parts[$part] = substr($from, $start, $length);
            $start += $length;
        }
        return implode('-', $parts);
    }

    /**
     * This method check's if the passed serialz is
     * valid.
     *
     * @param string $serialz The serialz to check
     * @return boolen TRUE if the serial is valid for the actual customer
     */
    public function isValid($serialz)
    {
        // decrypt the passed licence
        $this->decrypt($serialz);
        // check the start date of the serialz
        $from = new Zend_Date($this->_toIsoDate($this->_validFrom), Zend_Date::ISO_8601);
        if ($from->compare(Zend_Date::now()) == 1) {
            return false;
        }
        // check the runtime for the serialz
        $then = new Zend_Date($this->_toIsoDate($this->_validThru), Zend_Date::ISO_8601);
        if ($then->compare(Zend_Date::now()) == -1) {
            return false;
        }
        // check for the customer ID
        if ($this->_customerId != $this->_number + $this->_getCustomerId()) {
            return false;
        }
        // check fot the product ID
        if ($this->_productId != $this->_number + $this->_getProductId()) {
            return false;
        }
        // return TRUE if the serialz is valid
        return true;
    }

    /**
     * Returns the customer ID for handling
     * the serialz for.
     *
     * @return integer The ID of the customer
     */
    protected function _getCustomerId()
    {
        return $this->_linkPurchased->getCustomerId();
    }

    /**
     * Returns the product ID for handling
     * the serialz for.
     *
     * @return integer The ID of the product
     */
    protected function _getProductId()
    {
        return $this->_linkPurchased->getProductId();
    }
}