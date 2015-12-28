<?php

/**
 * Faett_Channel_Model_Resource_Eav_Mysql4_Setup
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
 * @category    Faett
 * @package     Faett_Channel
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */

/**
 * @category   	Faett
 * @package    	Faett_Channel
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Channel_Model_Resource_Eav_Mysql4_Setup
    extends Mage_Eav_Model_Entity_Setup {

    /**
     * Creates a new URL rewrite.
     *
     * @param string $idPath ID path - has to be unique
     * @param string $source The source path, e. g. channel.xml
     * @param string $target The target path, e. g. channel/index
     * @param integer $storeId The store ID to create the rewrite for
     * @param integer $isSystem TRUE if rewrite is as system wide rewrite
     * @return unknown_type
     */
    public function createUrlRewrite(
        $idPath,
        $source,
        $target,
        $storeId = 1,
        $isSystem = null) {
        $this->_conn->insert(
            'core_url_rewrite',
            array(
                'id_path' => $idPath,
                'request_path' => $source,
                'target_path' => $target,
                'store_id' => $storeId,
                'is_system' => $isSystem
            )
        );
    }

    /**
     * Add a new group
     *
     * @param mixed $entityTypeId
     * @param mixed $setId
     * @param string $name
     * @param int $sortOrder
     * @return integer The ID of the last created customer group
     */
    public function addCustomerGroup(
        $customerGroupCode,
        $taxClassId = 3) {

        $this->_conn->insert(
            'customer_group',
            array(
                'customer_group_code' => $customerGroupCode,
                'tax_class_id' => $taxClassId
            )
        );
        // load the ID of the last created group
        return $this->_conn->lastInsertId();
    }
}