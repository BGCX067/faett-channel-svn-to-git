<?php

/**
 * Faett_Channel_Helper_Data
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
class Faett_Channel_Model_Mysql4_Link
    extends Faett_Package_Model_Mysql4_Link {
        
    /**
     * Constant for storing the email template for the new release message.
     * @var string
     */
    const XML_PATH_NEW_RELEASE_EMAIL_TEMPLATE  = 'channel/new_release/email_template';
    
    /**
     * Perform actions after object save.
     *
     * @param Varien_Object $object The previously created link
     * @return Faett_Package_Model_Mysql4_Link The instance
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {   
        // check if a new Link was saved
        if ($object->isObjectNew()) {
            // if yes, initialize the model
            $linkUpdate = Mage::getModel('channel/link_update');
            $linkUpdate->loadByLinkIdFk($linkId = $object->getLinkId());
            // check if the update link was already set
            if (is_null($linkUpdate->getId())) {
                // if not, create a new link update entry
                $linkUpdate->setLinkIdFk($linkId);
                $linkUpdate->setCreatedAt(date('Y-m-d H:i:s'));
                $linkUpdate->save();
            }
        }
        // call the parent method       
        return parent::_afterSave($object);
    }
}