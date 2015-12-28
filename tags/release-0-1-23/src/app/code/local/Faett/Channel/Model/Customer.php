<?php

/**
 * Faett_Channel_Model_Customer
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
class Faett_Channel_Model_Customer
    extends Mage_Customer_Model_Customer {

    /**
     * Creates a new API user, copied from the maintainer data
     * that was actually created.
     *
     * @return Faett_Channel_Model_Customer The user created before
     */
    public function _afterSave()
    {
        // try to load an API user with the email of the created user
        $maintainer = Mage::getModel('channel/maintainer')
            ->loadByEmail($this->getEmail());
        // check if already an API user with the email exists
        if (!$maintainer->userExists()) {
            // if not, copy the user's data
            $maintainer->setFirstname($this->getFirstname());
            $maintainer->setLastname($this->getLastname());
            $maintainer->setUsername($this->hashPassword($this->getEmail()));
            $maintainer->setEmail($this->getEmail());
            // save with password copied from user
            $maintainer->saveWithCopiedPassword($this);
            // check if the default role ID is set
            if ($defaultRoleId = $this->_getDefaultRole()) {
                // set the role ID
                $maintainer->setRoleId($defaultRoleId);
                // check if the API user already has this role
                // (should usally never happen)
                if(!$maintainer->roleUserExists()) {
                    // add the user to the role
                    $maintainer->add();
                }
            }
        }
        // invoke the _afterSave() method of the parent class
        return parent::_afterSave();
    }

    /**
     * Returns the default API user role type set
     * in the configuration backend.
     *
     * @return integer The default role ID
     */
    protected function _getDefaultRole()
    {
        return Mage::getStoreConfig('channel/global/default_role');
    }
}