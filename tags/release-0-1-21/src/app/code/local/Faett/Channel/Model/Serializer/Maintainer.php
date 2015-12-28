<?php

/**
 * Faett_Channel_Model_Serializer_Maintainer
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
class Faett_Channel_Model_Serializer_Maintainer
    extends Varien_Object {

    /**
	 * Renders a list with all maintainers available in the
	 * system.
	 *
     * Passes the user currently logged into the system and
     * the maintainer the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Maintainer $maintainer
     * 		The maintainer to serializer has to be attached to
     * @return string XML representation of the resource
     */
    public function allmaintainers(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Maintainer $maintainer) {
        // initialize the serializer
        $serializer = new Faett_Channel_Serializer_Maintainer_AllMaintainers(
            $user,
            $maintainer
        );
        // return the XML representation
        return $serializer->serialize();
    }

    /**
	 * Renders detailed information about the passed
	 * maintainer.
	 *
     * Passes the user currently logged into the system and
     * the maintainer the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Maintainer $maintainer
     * 		The maintainer to serializer has to be attached to
     * @return string XML representation of the resource
     */
    public function info(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Maintainer $maintainer) {
        // initialize the serializer
        $serializer = new Faett_Channel_Serializer_Maintainer_Info(
            $user,
            $maintainer
        );
        // return the XML representation
        return $serializer->serialize();
    }
}