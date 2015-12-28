<?php

/**
 * Faett_Channel_Model_Category
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
class Faett_Channel_Model_Category
    extends Varien_Object {
    
    const CACHE_TAG = 'channel_category';

    const MODEL = 'channel/category';
    
    const REGISTRY = 'category';
    
    protected $_cacheTag         = 'channel_category';

    protected $_serializer = null;

    protected $_attribute = null;

    protected $_options = array();

    public function _construct()
    {

        $this->_attribute = Mage::getModel('eav/entity_attribute')->loadByCode(4, 'package_category');

        foreach ($this->_attribute->getFrontend()->getSelectOptions() as $option) {
            if (!empty($option['value'])) {
                $this->_options[$option['value']] = $option['label'];
            }
        }
    }

    public function setOptionValue($value)
    {
        $this->setData('optionValue', $value);
    }

    public function getOptionValue()
    {
        return $this->getData('optionValue');
    }

    /**
     * Load object data
     *
     * @param   integer $id
     * @return  Mage_Core_Model_Abstract
     */
    public function load($id, $field=null)
    {

        if (!array_key_exists($id, $this->_options)) {
            throw new Exception('Category ' . $id . ' not available');
        }

        $this->setId($id);
        $this->setOptionValue($this->_options[$id]);

        return $this;
    }

    /**
     * Load object data
     *
     * @param   integer $id
     * @return  Mage_Core_Model_Abstract
     */
    public function loadByValue($value)
    {

        $options = array_flip($this->_options);

        if (!array_key_exists($value, $options)) {
            throw new Exception('Category ' . $value . ' not available');
        }

        $this->setId($id = $options[$value]);
        $this->setOptionValue($this->_options[$id]);

        return $this;
    }

    public function setSerializer(
        Faett_Channel_Serializer_Interfaces_Serializer $serializer) {
        $this->_serializer = $serializer;
        return $this;
    }

    public function getSelectOptions()
    {
        return $this->_options;
    }

    /**
     * (non-PHPdoc)
     * @see lib/Varien/Varien_Object#__toXml($arrAttributes, $rootName, $addOpenTag, $addCdata)
     */
    public function __toXml(
        array $arrAttributes = array(),
        $rootName = 'm',
        $addOpenTag = false,
        $addCdata = true) {
        return $this->_serializer->serialize();
    }
}