<?php

/**
 * Faett_Channel_Serializer_Category_Abstract
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
abstract class Faett_Channel_Serializer_Category_Abstract
    extends Faett_Channel_Serializer_Abstract {

    /**
     * REST namespace for a category ressource.
     * @var string
     */
    const REST_CATEGORY = 'rest.category';

    /**
     * REST namespace for ressource with the category list.
     * @var string
     */
    const REST_ALLCATEGORIES = 'rest.allcategories';

    /**
     * REST namespace for ressource with the category packages list.
     * @var string
     */
    const REST_CATEGORYPACKAGES = 'rest.categorypackages';

    /**
     * The category the serializer has to be attached to
     * @var Faett_Channel_Model_Category
     */
    protected $_category = null;

    /**
     * Array with the available namespaces for this ressource type
     * @var array
     */
    protected $_namespaces = array(
        Faett_Channel_Serializer_Category_Abstract::REST_CATEGORY,
        Faett_Channel_Serializer_Category_Abstract::REST_ALLCATEGORIES,
        Faett_Channel_Serializer_Category_Abstract::REST_CATEGORYPACKAGES
    );

    /**
     * Passes the category the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Category $category
     * 		The category the serializer has to be attached to
     * @return void
     */
    public function __construct(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Category $category) {
        Faett_Channel_Serializer_Abstract::__construct($user);
        $this->_category = $category->setSerializer($this);
    }

    /**
     * (non-PHPdoc)
     * @see lib/Faett/Channel/Serializer/Faett_Channel_Serializer_Abstract#_getNamespaces()
     */
    protected function _getNamespaces()
    {
        return $this->_namespaces;
    }
}