<?php

/**
 * Faett_Channel_Model_Mysql4_Validation_Request
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
 * @category   	Faett
 * @package    	Faett_Channel
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Channel_Model_Mysql4_Validation_Request
    extends Mage_Core_Model_Mysql4_Abstract {

    /**
     * (non-PHPdoc)
     * @see lib/Varien/Varien_Object#_construct()
     */
    public function _construct()
    {
        // Note that the validation_request_id refers to the key field in your database table.
        $this->_init('channel/validation_request', 'validation_request_id');
    }

    /**
     * Loads the validation request by the passed data and returns
     * it.
     *
     * @param Faett_Channel_Model_Validation_Request $validationRequest 
     * 		The validation instance model to initialize
     * @param string $linkPurchasedIdFk The validation request's link purchased ID
     * @param string $ipAddress The validation request's IP address
     * @return Faett_Channel_Model_Validation_Request The initialized validation request
     */
    public function loadByLinkPurchasedIdFkAndIp(
        Faett_Channel_Model_Validation_Request $validationRequest,
        $linkPurchasedIdFk,
        $ipAddress) {
        // create an SQL to load the validation request by the passed data
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('channel/validation_request'))
            ->where('link_purchased_id_fk=:linkPurchasedIdFk')
            ->where('ip_address=:ipAddress');
        // execute the SQL and return the data
        if ($id = $this->_getReadAdapter()->fetchOne($select, array('linkPurchasedIdFk' => $linkPurchasedIdFk, 'ipAddress' => $ipAddress)))
        {
            // use the found data to initialize the instance
            $this->load($validationRequest, $id);
        }
    }
}