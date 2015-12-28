<?php

/**
 * Faett_Channel_Model_Encryption
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
class Faett_Channel_Model_Encryption extends Mage_Core_Model_Encryption 
{

    /**
     * Instantiate crypt model
     *
     * @param string $key
     * @return Varien_Crypt_Mcrypt
     */
    protected function _getCrypt($key = null, $iv = null)
    {
        if (!$this->_crypt) {
            // check if a key was passed
            if (null === $key) {
                $key = (string) Mage::getConfig()->getNode('global/crypt/key');
            }
            // check if an initialization vector was passed
            if (null === $iv) {
                // load the initialization vector
                $iv = (string) Mage::getStoreConfig(
                    Faett_Channel_Helper_Data::FAETT_CHANNEL_CRYPT_IV
                );
            }
            // initialize the encoder
            $this->_crypt = new Faett_Channel_Crypt_AES($key, $iv);
        }
        // return the crypt model
        return $this->_crypt;
    }


    /**
     * Hash a string
     *
     * @param string $data The string to encrypt
     * @return string The encrypted string
     */
    public function hash($data)
    {
        return $this->encrypt($data);
    }

    /**
     * Encrypt a string
     *
     * @param string $data
     * @return string
     */
    public function encrypt($data)
    {
        return $this->_getCrypt()->encrypt((string)$data);
    }

    /**
     * Decrypt a string
     *
     * @param string $data
     * @return string
     */
    public function decrypt($data)
    {
        return $this->_getCrypt()->decrypt((string)$data);
    }
}