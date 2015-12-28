<?php

/**
 * Faett_Channel_Model_Serializer_Release
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
class Faett_Channel_Model_Serializer_Release
    extends Varien_Object {

    /**
	 * Renders list with all releases available for the passed
	 * package.
	 *
     * Passes the user currently logged into the system and
     * the package the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Package $package
     * 		The package to serializer has to be attached to
     * @return string XML representation of the resource
     */
    public function allreleases(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Package $package) {
        // initialize the serializer
        $serializer = new Faett_Channel_Serializer_Release_AllReleases(
            $user,
            $package
        );
        // return the XML representation
        return $serializer->serialize();
    }

    /**
	 * Renders the version number for the latest release
	 * with stability 'alpha'.
	 *
     * Passes the user currently logged into the system and
     * the package the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Package $package
     * 		The package the serializer has to be attached to
     * @return string XML representation of the resource
     */
    public function alpha(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Package $package) {
        // initialize the serializer
        $serializer = new Faett_Channel_Serializer_Release_Alpha(
            $user,
            $package
        );
        // return the XML representation
        return $serializer->serialize();
    }

    /**
	 * Renders the version number for the latest release
	 * with stability 'beta'.
	 *
     * Passes the user currently logged into the system and
     * the package the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Package $package
     * 		The package the serializer has to be attached to
     * @return string XML representation of the resource
     */
    public function beta(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Package $package) {
        // initialize the serializer
        $serializer = new Faett_Channel_Serializer_Release_Beta(
            $user,
            $package
        );
        // return the XML representation
        return $serializer->serialize();
    }

    /**
	 * Renders an serialized array with the dependencies
	 * of the passed package.
	 *
     * Passes the user currently logged into the system and
     * the package the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Package $package
     * 		The package the serializer has to be attached to
     * @return string XML representation of the resource
     */
    public function deps(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Package $package) {
        // initialize the serializer
        $serializer = new Faett_Channel_Serializer_Release_Deps(
            $user,
            $package
        );
        // return the XML representation
        return $serializer->serialize();
    }

    /**
	 * Renders the version number for the latest release
	 * with stability 'devel'.
	 *
     * Passes the user currently logged into the system and
     * the package the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Package $package
     * 		The package the serializer has to be attached to
     * @return string XML representation of the resource
     */
    public function devel(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Package $package) {
        // initialize the serializer
        $serializer = new Faett_Channel_Serializer_Release_Devel(
            $user,
            $package
        );
        // return the XML representation
        return $serializer->serialize();
    }

    /**
	 * Renders the version number for the latest release.
	 *
     * Passes the user currently logged into the system and
     * the package the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Package $package
     * 		The package the serializer has to be attached to
     * @return string XML representation of the resource
     */
    public function latest(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Package $package) {
        // initialize the serializer
        $serializer = new Faett_Channel_Serializer_Release_Latest(
            $user,
            $package
        );
        // return the XML representation
        return $serializer->serialize();
    }

    /**
	 * Renders the package.xml for the requested release of
	 * the passed package.
	 *
     * Passes the user currently logged into the system and
     * the package the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Package $package
     * 		The package the serializer has to be attached to
     * @return string XML representation of the resource
     */
    public function package(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Package $package) {
        // initialize the serializer
        $serializer = new Faett_Channel_Serializer_Release_Package(
            $user,
            $package
        );
        // return the XML representation
        return $serializer->serialize();
    }

    /**
	 * Renders the version number for the latest release
	 * with stability 'stable'.
	 *
     * Passes the user currently logged into the system and
     * the package the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Package $package
     * 		The package the serializer has to be attached to
     * @return string XML representation of the resource
     */
    public function stable(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Package $package) {
        // initialize the serializer
        $serializer = new Faett_Channel_Serializer_Release_Stable(
            $user,
            $package
        );
        // return the XML representation
        return $serializer->serialize();
    }

    /**
	 * Renders detail informations for the requested release
	 * of the passed package.
	 *
     * Passes the user currently logged into the system and
     * the package the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Package $package
     * 		The package the serializer has to be attached to
     * @return string XML representation of the resource
     */
    public function version(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Package $package) {
        // initialize the serializer
        $serializer = new Faett_Channel_Serializer_Release_Version(
            $user,
            $package
        );
        // return the XML representation
        return $serializer->serialize();
    }
}