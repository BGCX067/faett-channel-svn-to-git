<?php

/**
 * Faett_Channel_Model_Maintainer
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
class Faett_Channel_Model_Maintainer
    extends Mage_Api_Model_User {

    const MODEL = 'channel/maintainer';

    protected $_serializer = null;

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
        $rootName = 'm',
        $addOpenTag = false,
        $addCdata = true) {
        return $this->_serializer->serialize();
    }

    /**
     * Loads the maintainer by it's email address.
     *
     * @param string $email The maintainer's email
     * @return Faett_Channel_Model_Maintainer The initialized maintainer
     */
    public function loadByEmail($email)
    {
        $this->setData($this->getResource()->loadByEmail($this, $email));
        return $this;
    }

    /**
     * This method creates a new maintainer by copying the data
     * from the passed customer.
     *
     * @param Mage_Customer_Model_Customer $customer
     * 		The customer to copy the values from
     * @return Faett_Channel_Model_Maintainer The maintainer itself
     */
    public function saveWithCopiedPassword(
        Mage_Customer_Model_Customer $customer) {
        // call the parent class's _beforeSave() method
        parent::_beforeSave();
        // initialize the array with the maintainers data to create
        $data = array(
                'firstname' => $this->getFirstname(),
                'lastname'  => $this->getLastname(),
                'email'     => $this->getEmail(),
                'modified'  => Mage::getSingleton('core/date')->gmtDate(),
                'is_active'	=> true
            );
        // check if an ID was set
        if ($this->getId() > 0) {
            $data['user_id'] = $this->getId();
        }
        // check if a username was set
        if ($this->getUsername()) {
            $data['username'] = $this->getUsername();
        }
        // copy the users password
        if ($customer->getPasswordHash()) {
            $data['api_key'] = $customer->getPasswordHash();
        }
        // set the data an save the maintainer
        $this->setData($data);
        $this->_getResource()->save($this);
        $this->_afterSave();
        // return the maintainer itself
        return $this;
    }

    /**
     * This method replaces the parent authenticate method and changes it
     * behaviour to use the API user's email address as username instaed
     * of the specified username.
     *
     * Authenticate email and api key and save loaded record in the session.
     *
     * @param string $username API user's email as username
     * @param string $apiKey The password
     * @return boolean TRUE if the passed credentials are valid
     */
    public function authenticate($username, $apiKey)
    {
        // load the API user by it's email address
        $this->loadByEmail($username);
        if (!$this->getId()) {
            return false;
        }
        // validate the api key (alias for the the password)
        $auth = Mage::helper('core')->validateHash($apiKey, $this->getApiKey());
        if ($auth) {
            // if the authentication was successfull return TRUE
            return true;
        } else {
            // else remove the API user's date from the session and return FALSE
            $this->unsetData();
            return false;
        }
    }
}