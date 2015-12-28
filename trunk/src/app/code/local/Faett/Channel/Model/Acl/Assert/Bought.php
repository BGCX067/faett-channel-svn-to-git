<?php

class Faett_Channel_Model_Acl_Assert_Bought 
	implements Zend_Acl_Assert_Interface {
	
	public function __construct() 
	{
		Mage::log(__METHOD__);	
	}
		
	/**
	 * (non-PHPdoc)
	 * @see Zend_Acl_Assert_Interface::assert()
	 */
	public function assert(
		Zend_Acl $acl, 
		Zend_Acl_Role_Interface $role = null, 
		Zend_Acl_Resource_Interface $resource = null,
        $privilege = null) {
        	
        Mage::log(__METHOD__);

        /*
        Mage::log(var_export($role, true));
        Mage::log(var_export($resource, true));
		Mage::log(var_export($acl, true));
        */
        
        return false;
	}
}