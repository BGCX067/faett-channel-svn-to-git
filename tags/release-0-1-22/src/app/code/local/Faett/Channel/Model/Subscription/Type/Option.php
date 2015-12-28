<?php

/**
 * Faett_Channel_Model_Subscription_Type_Option
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
class Faett_Channel_Model_Subscription_Type_Option
    extends Mage_Core_Model_Abstract {
	
	/**
	 * SKU for subscription type 'Trial'
	 * @var string
	 */
	const SKU_SUBSCRIPTION_TYPE_TRIAL 		= 'SUBSCRIPTION-01';
	
	/**
	 * SKU for subscription type '6 month'
	 * @var string
	 */
	const SKU_SUBSCRIPTION_TYPE_HALF_YEAR 	= 'SUBSCRIPTION-02';
	
	/**
	 * SKU for subscription type '1 year'
	 * @var string
	 */
	const SKU_SUBSCRIPTION_TYPE_YEAR 		= 'SUBSCRIPTION-03';
	
	/**
	 * SKU for subscription type '2 years'
	 * @var string
	 */
	const SKU_SUBSCRIPTION_TYPE_TWO_YEARS 	= 'SUBSCRIPTION-04';
	
	/**
	 * SKU for subscription type 'Endless'
	 * @var string
	 */
	const SKU_SUBSCRIPTION_TYPE_ENDLESS 	= 'SUBSCRIPTION-05';

    /**
     * (non-PHPdoc)
     * @see lib/Varien/Varien_Object#_construct()
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('channel/subscription_type_option');
    }

    /**
     * Load the option by its sku.
     *
     * @param string $sku The sku of the option to load
     * @return void
     */
    public function loadBySku($sku)
    {
        $this->_getResource()->loadBySku($this, $sku);
        return $this;
    }
}