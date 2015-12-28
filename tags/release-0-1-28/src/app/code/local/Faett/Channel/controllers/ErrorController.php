<?php

/**
 * Faett_Channel_ErrorController
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
 * @category   	Faett
 * @package    	Faett_Channel
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Channel_ErrorController
    extends Mage_Core_Controller_Front_Action {

	/**
	 * Sends an internal server error back to the client.
	 *
	 * @return void
	 */
	public function internalServerErrorAction()
	{
    	// set content type to XML
        $this->getResponse()->setHeader('HTTP/1.1','500 Internal Server Error');
        $this->getResponse()->setHeader('Status', '500 Internal Server Error');
        // load the layout
        $this->loadLayout();
        // append the the block to render the error message
        $block = $this->getLayout()->createBlock('Faett_Channel_Block_InternalServerError');
		$this->getLayout()->getBlock('content')->append($block);
		// render the layout itself
        $this->renderLayout();
	}

	/**
	 * Sends a message that the requested resource can not
	 * be found back to the client.
	 *
	 * @param string $content The message with the requested resource
	 * @return void
	 */
	public function notFoundAction()
	{
    	// set necessary headers        
        $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        $this->getResponse()->setHeader('Status','404 Not Found');
        // append the the block to render the error message
        $block = $this->getLayout()->createBlock('Faett_Channel_Block_NotFound');
		$this->getLayout()->getBlock('content')->append($block);
        // render the layout itself
        $this->renderLayout();
	}

	/**
	 * Requested HTTP basic authenthication.
	 *
	 * @return void
	 */
	public function unauthorizedAction()
	{
    	// load the channel name
	    $channelName = Mage::helper('channel')->getChannelName();
    	// set necessary headers  	
        $this->getResponse()->setHeader('WWW-Authenticate', 'Basic realm="' . $channelName . '"');
        $this->getResponse()->setHeader('HTTP/1.1', '401 Unauthorized');
        $this->getResponse()->setHeader('Status', '401 Unauthorized');
        // load the layout
        $this->loadLayout();
        // append the the block to render the error message
        $block = $this->getLayout()->createBlock('Faett_Channel_Block_Unauthorized');
		$this->getLayout()->getBlock('content')->append($block);
		// render the layout itself
        $this->renderLayout();
	}

	/**
	 * Sends a message back to the client that the user is not authorized
	 * to load the requested resource.
	 *
	 * @return void
	 */
	public function forbiddenAction()
	{
    	// set necessary headers  
        $this->getResponse()->setHeader('HTTP/1.1', '403 Forbidden');
        $this->getResponse()->setHeader('Status', '403 Forbidden');
        // load the layout
        $this->loadLayout();
        // append the the block to render the error message
        $block = $this->getLayout()->createBlock('Faett_Channel_Block_Forbidden');
		$this->getLayout()->getBlock('content')->append($block);
		// render the layout itself
        $this->renderLayout();
	}
}