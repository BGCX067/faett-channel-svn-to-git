<?php

/**
 * Faett_Channel_Model_Service
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
class Faett_Channel_Model_Service
	extends Mage_Core_Model_Abstract {

    /**
     * Constant for storing the email template for the new release message.
     * @var string
     */
    const XML_PATH_EMAILS_RELEASE_NEW  = 'channel/emails/release_new';

	/**
	 * Initialize magento module alias
	 *
	 * @return void
	 */
    protected function _construct()
    {
        $this->_init('channel/service');
    }

    /**
     * Syncs conferences with releated users to spreed
     *
     * @return TechDivision_SpreedSync_Model_Service
     */
	public function mail()
	{
	    // log that the mail service is executed
	    Mage::log('Now running mailservice!');
        // load a collection with the newly created links
	    $collection = Mage::getModel('channel/link_update')->getCollection();
        // send mails for each link
	    foreach ($collection as $item) {
			// load the purchased link
	        $link = Mage::getModel('package/link')->load($item->getLinkIdFk());
	        // load the previous releases of the product
            $linksPurchased = Mage::getResourceModel('package/link_purchased_collection')
                ->addFieldToFilter('product_id', $link->getProductId());
            // iterate over all previous releases
            foreach ($linksPurchased as $linkPurchasedId => $linkPurchased) {
                $linkPurchased->load($linkPurchasedId);
                // load the customer and send a email
                $customer = Mage::getModel('customer/customer')->load($linkPurchased->getCustomerId());
                $this->_sendNewReleaseEmail(
                    $customer,
                    $linkPurchased,
                    $link
                );
            }
            // remove the item after sending the email
            $item->delete();
	    }
	}

    /**
     * Send email with new release information.
     *
     * @param Mage_Customer_Model_Customer $customer
     * 		Customer to send the mail to
     * @param Faett_Package_Model_Link_Purchased $linkPurchased
     * 		The data about when the product has been purchased
     * @param Faett_Package_Model_Link $link
     * 		Data previously created link
     * @return Faett_Package_Model_Mysql4_Link
     * 		The instance
     */
    protected function _sendNewReleaseEmail(
        Mage_Customer_Model_Customer $customer,
        Faett_Package_Model_Link_Purchased $linkPurchased,
        Faett_Package_Model_Link $link) {
        try {
            // load the translation model
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);
            // initialize the store ID necessary for the translation
            $storeId = $customer->getStoreId();
            if ($customer->getWebsiteId() != '0' && $storeId == '0') {
                $storeIds = Mage::app()->getWebsite($customer->getWebsiteId())->getStoreIds();
                reset($storeIds);
                $storeId = current($storeIds);
            }
            // load the email template, generate and send the mail
            Mage::getModel('core/email_template')
                ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                ->sendTransactional(
                    Mage::getStoreConfig(Faett_Channel_Model_Service::XML_PATH_EMAILS_RELEASE_NEW),
                    Mage::getStoreConfig(Mage_Customer_Model_Customer::XML_PATH_REGISTER_EMAIL_IDENTITY),
                    $customer->getEmail(),
                    $customer->getName(),
                    array(
                    	'customer' => $customer,
                        'linkPurchased' => $linkPurchased,
                        'link' => $link
                    )
                );
            // close the translation model
            $translate->setTranslateInline(true);
        } catch(Exception $e) {
            Mage::logException($e);
        }
        // return the instance itself
        return $this;
    }

    /**
     * Loads the link for the passed product ID and version.
	 *
	 * @param Faett_Package_Model_Link $link The link to load
	 * @param integer $productId The product ID to load the link for
	 * @param string $version The version to load the link for
	 * @param void
     */
    public function loadByProductIdAndVersion(
        Faett_Package_Model_Link $link,
        $productId,
        $version) {
        // initialize the SQL for loading the link by its version
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('package/link'), array($this->getIdFieldName()))
            ->where('product_id=:productId')
            ->where('version=:version');
		// try to load the link title by its link ID
        if ($id = $this->_getReadAdapter()->fetchOne($select, array('productId' => $productId, 'version' => $version))) {
            // use the found data to initialize the instance
            $this->load($link, $id);
        }
    }
}