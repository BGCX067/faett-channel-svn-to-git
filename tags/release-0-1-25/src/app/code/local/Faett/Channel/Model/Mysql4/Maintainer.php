<?php

/**
 * Faett_Channel_Model_Mysql4_Maintainer
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
class Faett_Channel_Model_Mysql4_Maintainer
    extends Mage_Api_Model_Mysql4_User {

    /**
     * Loads the maintainer by it's email address and returns
     * it.
     *
     * @param Faett_Channel_Model_Maintainer $maintainer
     * 		The maintainer model to initialize
     * @param string $email The maintainer's email
     * @return Faett_Channel_Model_Maintainer The initialized maintainer
     */
    public function loadByEmail(
        Faett_Channel_Model_Maintainer $maintainer,
        $email) {
        // create an SQL to load the API user by it's email
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('api/user'))
            ->where('email=:email');
        // execute the SQL and return the data
        if ($id = $this->_getReadAdapter()->fetchOne($select, array('email' => $email)))
        {
            // use the found data to initialize the instance
            $this->load($maintainer, $id);
        }
    }
}