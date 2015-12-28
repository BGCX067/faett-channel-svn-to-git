<?php

/**
 * Faett_Channel_Crypt_AES
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
 * Class with Encrypt- and Decrypt-Functions.
 * 
 * @category   Faett
 * @package    Faett_Channel
 * @copyright  Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    <http://www.gnu.org/licenses/> 
 * 			   GNU General Public License (GPL 3)
 * @author     Tim Wagner <tw@faett.net>
 */
class Faett_Channel_Crypt_AES
{

	/**
	 * Data representation as passed.
	 * @var integer
	 */
	const DATA_AS_IS = 0;

	/**
	 * Data representation base64 encoded.
	 * @var integer
	 */
	const DATA_AS_BASE64 = 1;

	/**
	 * Data representation hex encoded.
	 * @var integer
	 */
	const DATA_AS_HEX = 2;

	/**
	 * The encryption key.
	 * @var string
	 */
	private $_key = '';

	/**
	 * The initialization vector.
	 * @var string
	 */
	private $_iv = '';

	/**
	 * Constructor to initialize the class with the encryption key
	 * and the initialization vector.
	 *
	 * @param string $key The encryption key to use
	 * @param string $iv The initialization vector to use
	 * @return void
	 */
	public function __construct($key, $iv)
	{
	    $this->_key = $key;
	    $this->_iv = $iv;
	}

	/**
	 * Adds pkcs5 padding.
	 *
	 * @return Given text with pkcs5 padding
	 * @param string $data String to pad
	 * @param integer $blocksize Blocksize used by encryption
	 */
	private function _pkcs5Pad($data, $blocksize)
	{
		$pad = $blocksize - (strlen($data) % $blocksize);
		$returnValue = $data . str_repeat(chr($pad), $pad);
		return $returnValue;
	}

	/**
	 * Removes padding.
	 *
	 * @return Given text with removed padding characters
	 * @param string $data String to unpad
	 */
	private function _pkcs5Unpad($data)
	{
		$pad = ord($data{strlen($data)-1});
		if ($pad > strlen($data)) return false;
		if (strspn($data, chr($pad), strlen($data) - $pad) != $pad) return false;

		return substr($data, 0, -1 * $pad);
	}

	/**
	 * Encrypts a string with the Advanced Encryption Standard.
	 *
	 * The used algorythm (cipher) is MCRYPT_RIJNDAEL_128 and the mode is
	 * 'cbc' (cipher block chaining).
	 *
	 * @return Encrypted text as hexadecimal representation
	 * @param string $data String to encrypt
	 * @param  integer $dataAs [optional]
	 *   	Encode data after encryption as (TechDivision_Crypt_AES::DATA_AS_*)
	 *   	- Default TechDivision_Crypt_AES::DATA_AS_IS
	 */
	public function encrypt($data, $dataAs = 2)
	{
        // load the size and the cipher itself
		$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
		$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
		// add padding to string
		$data = $this->_pkcs5Pad($data, $size);
		$length = strlen($data);
        // initialize the encryption module
		mcrypt_generic_init($cipher, $this->_key, $this->_getIv($cipher));
        // encrypt the string
		$data = mcrypt_generic($cipher, $data);
		mcrypt_generic_deinit($cipher);
        // check the return format
		if($dataAs == Faett_Channel_Crypt_AES::DATA_AS_HEX) {
			$data = bin2hex($data);
		} else if ($dataAs == Faett_Channel_Crypt_AES::DATA_AS_BASE64) {
			$data = base64_encode($data);
		}
        // return the encrypted string
		return $data;
	}

	/**
	 * Decrypts a string with the Advanced Encryption Standard.
	 *
	 * The used algorythm (cipher) is MCRYPT_RIJNDAEL_128 and the mode is
	 * 'cbc' (cipher block chaining).
	 *
	 * @return Decrypted text
	 * @param string $data String to decrypt as hexadecimal representation
	 * @param  integer $dataAs [optional]
	 *   	Decode data before decryption as (TechDivision_Crypt_AES::DATA_AS_*)
	 *   	- Default TechDivision_Crypt_AES::DATA_AS_IS
	 */
	public function decrypt($data, $dataAs = 2)
	{
        // load the size and the cipher itself
		$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
		$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
        // initialize the encryption module
		mcrypt_generic_init($cipher, $this->_getKey(), $this->_getIv());
        // check the return format
		if($dataAs == Faett_Channel_Crypt_AES::DATA_AS_HEX) {
			// pack() is used to convert hex string to binary
			$data = pack('H*', $data);
		} else if ($dataAs == Faett_Channel_Crypt_AES::DATA_AS_BASE64) {
			$data = base64_decode($data);
		}
        // decrypt the password
		$data = mdecrypt_generic($cipher, $data);
		mcrypt_generic_deinit($cipher);
        // unpad the decrypted string
		return $this->_pkcs5Unpad($data);
	}

    /**
     * Returns the encryption key to use.
     *
     * @return The encryption key itself
     */
	protected function _getKey()
	{
        return $this->_key;
	}

    /**
     * Returns the initialization vector to use.
     *
     * @return The initialization vector itself
     */
	protected function _getIv()
	{
        return $this->_iv;
	}
}