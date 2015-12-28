<?php

/**
 * Faett_Channel_Model_Serializer_Channel
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
class Faett_Channel_Model_Serializer_Channel
    extends Varien_Object {

    /**
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Maintainer $maintainer
     * 		The maintainer the serializer has to be attached to
     * @param string $serializerMethodName
     * 		The serializer's method to be invoked
     * @return string XML representation of the resource
     * @throws Faett_Channel_Exceptions_InvalidResourcePathException
     * 		Is thrown if the requested serializer method can't be found
     */
    public function m(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Maintainer $maintainer,
        $serializerMethodName) {
		// reflect the package serializer
        $reflectionClass = new ReflectionClass('Faett_Channel_Model_Serializer_Maintainer');
        // check if the method exists
        if ($reflectionClass->hasMethod($serializerMethodName)) {
        	$reflectionMethod = $reflectionClass->getMethod($serializerMethodName);
        	// if yes, render and return the XML representation
        	return $reflectionMethod->invoke($reflectionClass->newInstance(), $user, $maintainer);
        }
        // throw an exception because serializer method can't be found
        throw Faett_Channel_Exceptions_InvalidResourcePathException::create(
        	'Serializer method ' . $serializerMethod . ' can\'t be found',
            '200.error.acl.invalid-serializer-method'
        );
    }

    /**
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Package $package
     * 		The package the serializer has to be attached to
     * @param string $serializerMethodName
     * 		The serializer's method to be invoked
     * @return string XML representation of the resource
     * @throws Faett_Channel_Exceptions_InvalidResourcePathException
     * 		Is thrown if the requested serializer method can't be found
     */
    public function p(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Package $package,
        $serializerMethodName) {
		// reflect the package serializer
        $reflectionClass = new ReflectionClass('Faett_Channel_Model_Serializer_Package');
        // check if the method exists
        if ($reflectionClass->hasMethod($serializerMethodName)) {
        	$reflectionMethod = $reflectionClass->getMethod($serializerMethodName);
        	// if yes, render and return the XML representation
        	return $reflectionMethod->invoke($reflectionClass->newInstance(), $user, $package);
        }
        // throw an exception because serializer method can't be found
        throw Faett_Channel_Exceptions_InvalidResourcePathException::create(
        	'Serializer method ' . $serializerMethod . ' can\'t be found',
            '200.error.acl.invalid-serializer-method'
        );
    }

    /**
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Category $category
     * 		The category the serializer has to be attached to
     * @param string $serializerMethodName
     * 		The serializer's method to be invoked
     * @return string XML representation of the resource
     * @throws Faett_Channel_Exceptions_InvalidResourcePathException
     * 		Is thrown if the requested serializer method can't be found
     */
    public function c(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Category $category,
        $serializerMethodName) {
		// reflect the package serializer
        $reflectionClass = new ReflectionClass('Faett_Channel_Model_Serializer_Category');
        // check if the method exists
        if ($reflectionClass->hasMethod($serializerMethodName)) {
        	$reflectionMethod = $reflectionClass->getMethod($serializerMethodName);
        	// if yes, render and return the XML representation
        	return $reflectionMethod->invoke($reflectionClass->newInstance(), $user, $category);
        }
        // throw an exception because serializer method can't be found
        throw Faett_Channel_Exceptions_InvalidResourcePathException::create(
        	'Serializer method ' . $serializerMethod . ' can\'t be found',
            '200.error.acl.invalid-serializer-method'
        );
    }

    /**
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Package $package
     * 		The package the serializer has to be attached to
     * @param string $serializerMethodName
     * 		The serializer's method to be invoked
     * @return string XML representation of the resource
     * @throws Faett_Channel_Exceptions_InvalidResourcePathException
     * 		Is thrown if the requested serializer method can't be found
     */
    public function r(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Package $package,
        $serializerMethodName) {
		// reflect the package serializer
        $reflectionClass = new ReflectionClass('Faett_Channel_Model_Serializer_Release');
        // check if the method exists
        if ($reflectionClass->hasMethod($serializerMethodName)) {
        	$reflectionMethod = $reflectionClass->getMethod($serializerMethodName);
        	// if yes, render and return the XML representation
        	return $reflectionMethod->invoke($reflectionClass->newInstance(), $user, $package);
        }
        // throw an exception because serializer method can't be found
        throw Faett_Channel_Exceptions_InvalidResourcePathException::create(
        	'Serializer method ' . $serializerMethod . ' can\'t be found',
            '200.error.acl.invalid-serializer-method'
        );
	}
}