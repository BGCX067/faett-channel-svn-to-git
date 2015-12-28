<?php

/**
 * Faett_Channel_AllTests
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

// set the include path necessary for running the tests
set_include_path(
	get_include_path() . PATH_SEPARATOR
	. getcwd() . PATH_SEPARATOR
	. 'pear/php'
);

require_once 'Faett/Channel/Model/ChannelTest.php';

/**
 * The test suite.
 *
 * @category   	Faett
 * @package    	Faett_Channel
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Channel_AllTests {

    /**
     * Initializes the TestSuite.
     *
     * @return Faett_XHProfPHPUnit_TestSuite The initialized TestSuite
     */
    public static function suite() {
        // initialize the TestSuite
        $suite = new PHPUnit_Framework_TestSuite('${ant.project.name}');
        $suite->addTestSuite('Faett_Channel_Model_ChannelTest');
        // return the initialized suite
        return $suite;
    }
}