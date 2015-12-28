<?php
/**
 * Faett_Channel_Block_Adminhtml_Catalog_Product_Edit_Tab_Serialz
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
 * Adminhtml catalog product downloadable items tab and form
 * 
 * @category   	Faett
 * @package    	Faett_Channel
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Channel_Block_Adminhtml_Catalog_Product_Edit_Tab_Serialz
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_serialz_grid');
        $this->setDefaultSort('created_at', 'desc');
        $this->setUseAjax(false);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('package/link_purchased_collection')
            ->addFieldToFilter(
            	'customer_id',
                Mage::registry('current_customer')->getEntityId());

        foreach ($collection as $purchasedLink) {

            $purchasedLink->setPackageName(
                $purchasedLink->getProduct()->getName()
            );

            $serialzCrypted = $purchasedLink->getSerialz();

            if (!empty($serialzCrypted)) {
                $serialz = Mage::getModel('package/serialz')->init($purchasedLink)->decrypt($serialzCrypted);
                $purchasedLink->setValidFrom($serialz->getValidFrom());
                $purchasedLink->setValidThru($serialz->getValidThru());
            }
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('channel')->__('Purchase #'),
            'width'     => '100px',
            'index'     => 'purchased_id',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('channel')->__('Purchased On'),
            'index'     => 'created_at',
            'type'      => 'datetime',
            'width'     => '120px'
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('channel')->__('Product Name'),
            'index'     => 'product_name'
        ));

        $this->addColumn('serialz', array(
            'header'    => Mage::helper('channel')->__('Serial'),
            'index'     => 'serialz',
        ));

        $this->addColumn('shipping_name', array(
            'header'    => Mage::helper('channel')->__('Valid From'),
            'index'     => 'valid_from',
            'type'      => 'date',
            'sortable'	=> false,
            'filter'    => false,
            'width'     => '80px'
        ));

        $this->addColumn('grand_total', array(
            'header'    => Mage::helper('channel')->__('Valid Thru'),
            'index'     => 'valid_thru',
            'type'      => 'date',
            'sortable'	=> false,
            'filter'    => false,
            'width'     => '80px'
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
        	'*/licencserver/serializ',
            array(
            	'purchased_id' => $row->getId()
            )
        );
    }

    public function getGridUrl()
    {
        return $this->getUrl(
        	'*/*/serializ',
            array(
            	'_current' => true
            )
        );
    }

    public function getTabLabel()
    {
        return Mage::helper('channel')->__('Serialz View');
    }

    public function getTabTitle()
    {
        return Mage::helper('channel')->__('Serialz View');
    }

    public function canShowTab()
    {
        if (Mage::registry('current_customer')->getId()) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        if (Mage::registry('current_customer')->getId()) {
            return false;
        }
        return true;
    }
}