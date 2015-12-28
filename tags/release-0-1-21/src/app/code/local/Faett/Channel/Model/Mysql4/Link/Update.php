<?php

/**
 * Faett_Channel_Model_Mysql4_Link_Update
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
class Faett_Channel_Model_Mysql4_Link_Update
    extends Mage_Core_Model_Mysql4_Abstract {

    /**
     * (non-PHPdoc)
     * @see lib/Varien/Varien_Object#_construct()
     */
    public function _construct()
    {
        // Note that the link_update_id refers to the key field in your database table.
        $this->_init('channel/link_update', 'link_update_id');
    }
    
    /**
     * Loads the link update with the passed link ID.
     * 
     * @param Faett_Channel_Model_Link_Update The link update instance to initialize
     * @param integer $linkIdFk The link ID to load the link update for
     * @return void
     */
    public function loadByLinkIdFk(
        Faett_Channel_Model_Link_Update $linkUpdate,
        $linkIdFk) {
        // create an SQL to load the link update by the passed link ID
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('channel/link_update'))
            ->where('link_id_fk=:linkIdFk');
        // execute the SQL and return the data
        if ($id = $this->_getReadAdapter()->fetchOne($select, array('linkIdFk' => $linkIdFk)))
        {
            // use the found data to initialize the instance
            $this->load($linkUpdate, $id);
        }
    }
}