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
class Faett_Channel_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Configuration path for the ID of the maintainer's customer group.
     * @var string
     */
    const MAINTAINER_GROUP_ID = 'faett/channel/maintainer_group_id';

    /**
     * Registration key for AES initialization vector.
     * @var string
     */
    const FAETT_CHANNEL_CRYPT_IV = 'faett/channel/crypt/iv';

    /**
     * Request key for the ID parsed from the mapped resource URL.
     * @var string
     */
    const ID = 'id';

    /**
     * Request key for the resource type parsed from the mapped resource URL.
     * @var string
     */
    const TYPE = 'type';

    /**
     * Request key for the resource version parsed from the mapped resource URL.
     * @var string
     */
    const VERSION = 'version';

    /**
     * Request key for the content type parsed from the mapped resource URL.
     * @var string
     */
    const CONTENT_TYPE = 'contentType';

    /**
     * Request key for the resource type, can be 'c', 'm', 'p' or 'r'.
     * @var string
     */
    const RESOURCE = 'resource';

    /**
     * Array with the available content types.
     * @var array
     */
    protected $_contentTypes = array(
        'txt' => 'text/plain',
        'xml' => 'application/xml'
    );

    /**
     * Array with the allowed ressource types.
     * @var array
     */
    protected $_types = array('channel');

    /**
     * The default type key.
     * @var string
     */
    protected $_defaultType = 'r/version';

    /**
     * The mapping resource => class.
     * @var array
     */
    protected $_acls = array(
        'c/categories',
        'c/packages',
        'c/info',
        'c/packagesinfo',
        'm/allmaintainers',
        'm/info',
        'p/packages',
        'p/info',
        'p/maintainers',
        'r/allreleases',
        'r/alpha',
        'r/beta',
        'r/deps',
        'r/devel',
        'r/latest',
        'r/package',
        'r/stable',
        'r/version'
    );

    protected $_protectedAcls = array(
        'r/version'
    );

    /**
     * Initializes the helper with an array of
     * allowed ressource types.
     *
     * @return void
     */
    public function __construct()
    {
        // iterate over the available serializers and build an array
        // with the allowed resources from the type keys.
        foreach($this->getAcls() as $type) {
            if (!in_array($t = substr($type, 2), $this->_types)) {
                $this->_types[] = $t;
            }
        }
    }

    /**
     * Returns the default Serializer type.
     *
     * @return string The default Serializer type
     */
    public function _getDefaultType()
    {
        return $this->_defaultType;
    }

    /**
     * This method returns TRUE if the passed type is available or
     * FALSE if not.
     *
     * @param string $key The type key to check
     * @return unknown_type
     */
    public function _isType($key)
    {
        return in_array($key, $this->_acls);
    }

    /**
     * Returns the regular expressions for resolving the
     * serializer classes for the requested ressource.
     *
     * @return array
     */
    protected function _regex()
    {
        // return all regular expressions
        return array(
        	'/(?P<'. Faett_Channel_Helper_Data::TYPE . '>channel)\.(?P<'. Faett_Channel_Helper_Data::CONTENT_TYPE . '>xml)/',
        	'/\/channel\/index\/(?P<'. Faett_Channel_Helper_Data::RESOURCE . '>[m|r|p|c])\/(?P<'. Faett_Channel_Helper_Data::TYPE . '>' . implode('|', $this->_types) . ')\.(?P<'. Faett_Channel_Helper_Data::CONTENT_TYPE . '>xml)/',
        	'/\/channel\/index\/(?P<'. Faett_Channel_Helper_Data::RESOURCE . '>[m|r|p|c])\/(?P<'. Faett_Channel_Helper_Data::ID . '>\w+)\/(?P<'. Faett_Channel_Helper_Data::TYPE . '>' . implode('|', $this->_types) . ')\.(?P<'. Faett_Channel_Helper_Data::CONTENT_TYPE . '>xml)/',
        	'/\/channel\/index\/(?P<'. Faett_Channel_Helper_Data::RESOURCE . '>[m|r|p|c])\/(?P<'. Faett_Channel_Helper_Data::ID . '>\w+)\/(?P<'. Faett_Channel_Helper_Data::TYPE . '>' . implode('|', $this->_types) . ')\.(?P<'. Faett_Channel_Helper_Data::CONTENT_TYPE . '>txt)/',
        	'/\/channel\/index\/(?P<'. Faett_Channel_Helper_Data::RESOURCE . '>[m|r|p|c])\/(?P<'. Faett_Channel_Helper_Data::ID . '>\w+)\/(?P<'. Faett_Channel_Helper_Data::TYPE . '>' . implode('|', $this->_types) . ')\.(?P<'. Faett_Channel_Helper_Data::VERSION . '>.*)\.(?P<'. Faett_Channel_Helper_Data::CONTENT_TYPE . '>xml)/',
        	'/\/channel\/index\/(?P<'. Faett_Channel_Helper_Data::RESOURCE . '>[m|r|p|c])\/(?P<'. Faett_Channel_Helper_Data::ID . '>\w+)\/(?P<'. Faett_Channel_Helper_Data::TYPE . '>' . implode('|', $this->_types) . ')\.(?P<'. Faett_Channel_Helper_Data::VERSION . '>.*)\.(?P<'. Faett_Channel_Helper_Data::CONTENT_TYPE . '>txt)/',
        	'/\/channel\/index\/(?P<'. Faett_Channel_Helper_Data::RESOURCE . '>[m|r|p|c])\/(?P<'. Faett_Channel_Helper_Data::ID . '>\w+)\/(?P<'. Faett_Channel_Helper_Data::VERSION . '>[0-9]\.[0-9]\.[0-9]{1,2})\.(?P<'. Faett_Channel_Helper_Data::CONTENT_TYPE . '>xml)/'
        );
    }

    /**
     * Generates and returns a random password with the
     * length passed as parameter.
     *
     * @param integer $length The length of the password to generate
     * @return string The random password itself
     */
    public function randomPassword($length = 8)
    {
        return substr(md5(uniqid(rand(), true)), 0, $length);
    }

    /**
     * Returns the channel's name with prefixed with http://
     *
     * @return string
     */
    public function getChannelName()
    {
        return str_replace(
        	'http://',
        	'',
            substr(Mage::getBaseUrl(), 0, strlen(Mage::getBaseUrl()) - 1)
        );
    }

    /**
     * Returns the channel's REST url.
     *
     * @return string
     */
    public function getRESTUrl()
    {
        return $this->_getUrl('channel/index');
    }

    /**
     * This method maps the requested resource to the Serializer
     * necessary for rendering the resource's representation.
     *
     * @param string $url Resource URL to map
     * @return array The mapping data
     */
    public function resolve($url)
    {	
        // array for the URL parameters
        $params = array();
        // array for the mapped return values
        $queryParams = array(
            Faett_Channel_Helper_Data::TYPE => $this->_getDefaultType()
        );
        // iterate over the REGEX's and try to map the data
        foreach ($this->_regex() as $regex) {
            if (preg_match($regex, $url, $params) > 0) {
                if (array_key_exists(Faett_Channel_Helper_Data::CONTENT_TYPE, $params)) {
                    $queryParams[Faett_Channel_Helper_Data::CONTENT_TYPE] =
                        $this->_contentTypes[$params[Faett_Channel_Helper_Data::CONTENT_TYPE]];
                }
                if (array_key_exists(Faett_Channel_Helper_Data::ID, $params)) {
                    $queryParams[Faett_Channel_Helper_Data::ID] =
                        $params[Faett_Channel_Helper_Data::ID];
                }
                if (array_key_exists(Faett_Channel_Helper_Data::VERSION, $params)) {
                    $queryParams[Faett_Channel_Helper_Data::VERSION] =
                        $params[Faett_Channel_Helper_Data::VERSION];
                }
                if (array_key_exists(Faett_Channel_Helper_Data::TYPE, $params)) {
                    // initialize the type key
                    $typeKey = $params[Faett_Channel_Helper_Data::TYPE];
                    // check if a resource can be found (not for channel.xml)
                    if (array_key_exists(Faett_Channel_Helper_Data::RESOURCE, $params)) {
                        $typeKey = $params[Faett_Channel_Helper_Data::RESOURCE] . '/' . $typeKey;
                    }
                    // set the found type key
                    if ($this->_isType($typeKey)) {
                        $queryParams[Faett_Channel_Helper_Data::TYPE] = $typeKey;
                    }
                }
                // return the mapped values
                return $queryParams;
            }
        }
        // throw an exception, because resource can not be mapped
        throw Faett_Channel_Exceptions_ResourceNotFoundException::create(
            'The requested URL ' . $url . ' was not found on this server',
            '300.error.no-url-resource-mapping'
        );
    }

    public function getAcls()
    {
        return $this->_acls;
    }

    public function getProtectedAcls()
    {
        return $this->_protectedAcls;
    }

    public function isValidAcl($resource)
    {
        return in_array(
            substr($resource, 8, strlen($resource)),
            $this->getAcls()
        );
    }

    public function isProtectedAcl($resource)
    {
        return in_array(
            substr($resource, 8, strlen($resource)),
            $this->getProtectedAcls()
        );
    }

    public function setCustomOption(
        $productId,
        $title,
        array $optionData,
        array $values = array()) {

		// Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);

		$defaultData = array(
			'type'			=> 'field',
			'is_require'	=> 0,
			'price'			=> 0,
			'price_type'	=> 'fixed',
		);

		$data = array_merge(
		    $defaultData,
		    $optionData,
		    array(
    			'product_id' 	=> (integer) $productId,
    			'title'			=> $title,
    			'values'		=> $values,
		    )
		);

		return $data;
	}

    /**
     * Returns the collection with the channels products.
     *
     * @param Faett_Channel_Model_Channel $channel
     * 		The channel to load the packages for
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     * 		The collection with the products
     */
    public function getPackageCollection(
    	Faett_Channel_Model_Channel $channel) {
        // load the root category of the actual store group
        $categoryId = Mage::app()
            ->getStore($channel->getId())
            ->getGroup()
            ->getRootCategoryId();        
        // log the ID of the root category
        Mage::log('Found root category: ' . $categoryId);
        // load the category
        $category = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($categoryId);
        // log the size of the products in the category
        Mage::log(
        	'Found ' . sizeof($category->getProductCollection()) . ' products'
        );
        // check if the category can be shown
        if ($this->_canShow($category)) {
            // if yes, return the products as Collection
            return $category
            	->getProductCollection()
            	->addFieldToFilter(
	            	'type_id',
	                Faett_Package_Model_Product_Type::TYPE_PACKAGE
	            );
        }
        // if not, return an empty Collection
        return new Varien_Data_Collection();
    }

    /**
     * Check if a category can be shown or not.
     *
     * @param  Mage_Catalog_Model_Category $category
     * 		The category to check
     * @return boolean TRUE if the category can be shown
     */
    protected function _canShow(Mage_Catalog_Model_Category $category)
    {
        // check if the category is new
        if (!$category->getId()) {
            // if yes, return FALSE
            return false;
        }
        // check if the category is active
        if (!$category->getIsActive()) {
            // if yes, return FALSE
            return false;
        }
        // else return TRUE
        return true;
    }
}