<?php

/**
 * Faett_Channel_Serializer_Abstract
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
abstract class Faett_Channel_Serializer_Abstract
    implements Faett_Channel_Serializer_Interfaces_Serializer {

    /**
     * The user logged into the system
     * @var Mage_Api_Model_User
     */
    protected $_user = null;

    /**
     * The XSD namespace to use for  rendering the ressource
     * @var string
     */
    protected $_namespace = '';

    /**
     * Passes the user for validation purposes.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @return void
     */
    public function __construct(Mage_Api_Model_User $user) {
        $this->_user = $user;
    }

    /**
     * Sets the namespace to use for rendering the ressource and
     * checks if the passed namespace is valid for the requested
     * ressource.
     *
     * @param string $namespace The namespace to set
     * @return void
     * @throws Faett_Channel_Exceptions_InvalidNamespaceException
     * 		Is thrown if an invalid namespace was passed
     */
    public function setNamespace($namespace)
    {
        // check if the namespace is valid
        if (!in_array($namespace, $this->_getNamespaces())) {
            throw Faett_Channel_Exceptions_InvalidNamespaceException::create(
            	'Invalid namespace ' . $namespace
            );
        }
        // initialize the namespace
        $this->_namespace = 'http://pear.php.net/dtd/' . $namespace;
    }

    /**
     * (non-PHPdoc)
     * @see src/lib/Faett/Channel/Serializer/Interfaces/Faett_Channel_Serializer_Interfaces_Serializer#getUser()
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Returns the valid namespaces for the ressource of the
     * Serializer implementation.
     *
     * @return array The valid namespaces as array
     */
    protected abstract function _getNamespaces();
}