<?php

/**
 * Faett_Channel_Model_Config_Source_Roles
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
class Faett_Channel_Model_Config_Source_Roles
{
    /**
     * Load the available roles to render the options
     * for a drop-down box in the configuration panel.
     *
     * @return array Roles for as options array
     */
    public function toOptionArray()
    {
        // load the roles
        $roles = Mage::getModel('api/role')->getCollection();
        // initialize the array for the options
        $options = array('' => Mage::helper('adminhtml')
        	->__('-- Please Select --'));
       	// initialize the options
        foreach ($roles as $id => $role) {
       	    $role->load($id);
       		$options[] = array(
                'value' => $role->getId(),
                'label' => $role->getRoleName()
            );
       	}
       	// return the options
        return $options;
    }
}