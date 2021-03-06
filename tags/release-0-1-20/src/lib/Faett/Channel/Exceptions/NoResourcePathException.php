<?php

/**
 * Faett_Channel_Exceptions_NoResourcePathException
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

require_once 'Faett/Core/Exceptions/AbstractException.php';

/**
 * @category   Faett
 * @package    Faett_Channel
 * @copyright  Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    <http://www.gnu.org/licenses/> 
 * 			   GNU General Public License (GPL 3)
 * @author     Tim Wagner <tw@faett.net>
 */
class Faett_Channel_Exceptions_NoResourcePathException
    extends Faett_Core_Exceptions_AbstractException
    implements Faett_Channel_Exceptions_Interfaces_AuthorizationException {

    /**
     * Creates and returns a new Exception instance.
     *
     * @param string $message The message itself
     * @param string $key The key for I18N
     * @return Faett_Channel_Exceptions_NoResourcePathException
     */
    public static function create($message, $key = '') {
        // create a new message
        $e = new Faett_Channel_Exceptions_NoResourcePathException($message);
        // set the message key
        $e->_setKey($key);
        // return the message
        return $e;
    }
}