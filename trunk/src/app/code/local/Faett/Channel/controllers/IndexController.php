<?php

/**
 * Faett_Channel_IndexController
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
class Faett_Channel_IndexController
    extends Mage_Core_Controller_Front_Action {

    /**
     * Request key for the username.
     * @var string
     */
    const PHP_AUTH_USER = 'PHP_AUTH_USER';

    /**
     * Request key for the password.
     * @var string
     */
    const PHP_AUTH_PW = 'PHP_AUTH_PW';

    /**
     * Resolved parameters for the actual resource.
     * @var array
     */
    protected $_params = array();

    /**
     * Flag for authentication to switch on or off.
     * @var boolean
     */
    protected $_authentication = false;

    /**
     * Initializes the class.
     *
     * @return void
     */
    public function __construct(
        Zend_Controller_Request_Abstract $request,
        Zend_Controller_Response_Abstract $response,
        array $invokeArgs = array()) {
        // call the parent constructor
        parent::__construct($request, $response, $invokeArgs);
    }

    /**
     * Predispatch: shoud set layout area
     *
     * @return Mage_Core_Controller_Front_Action
     */
    public function preDispatch()
    {
        try {
            // call the parents class method
            parent::preDispatch();    
			// load the channel
			$channel = Mage::getModel('channel/channel')
				->load(Mage::app()->getStore()->getId());
            // check if the channel is activated
			if (!$channel->isChannel()) {
				throw Faett_Channel_Exceptions_ChannelNotActivatedException::create(
	            	'The store is not activated as channel',
	            	'200.error.channel-not-activated'
				);
			}
            // resolve the needed parameters from the requested resource name
            $this->_params = Mage::helper('channel')->resolve(
                $this->getRequest()->getRequestString()
            );
            // return the instance itself
            return $this;
        } catch(Faett_Channel_Exceptions_ChannelNotActivatedException $cnae) {
	        // log the exception
		    Mage::logException($cnae);
			// if not, foward to the default 404 page
			$this->_forward('noRoute', 'default');
        } catch(Faett_Channel_Exceptions_ResourceNotFoundException $rnfe) {
	        // log the exception
		    Mage::logException($rnfe);
		    // register the error message
		    Mage::register(
		    	Faett_Channel_Block_NotFound::MESSAGE,
		    	$this->_getHelper()->__($rnfe->getKey())
		    );
			// forward to the not found page
		    $this->_forward('notFound', 'error', 'channel');
		} catch(Exception $e) {
	        // log the exception
		    Mage::logException($e);
		    // register the error message
		    Mage::register(
		    	Faett_Channel_Block_InternalServerError::MESSAGE,
		    	$e->getMessage()
		    );
			// forward to the internal server error page
		    $this->_forward('internalServerError', 'error', 'channel');
		}
    }

	/**
	 * Returns the channel's helper.
	 *
	 * @return Faett_Channel_Helper_Data The helper instance
	 */
	protected function _getHelper()
	{
        return Mage::helper('channel');
	}

    /**
     * Checks if user is already registered in the session, if
     * not a header is sent to request HTTP Basic Authentication.
     *
     * @return void
     */
    protected function _authenticate()
    {
        // load authentication flag from backend
        $this->_authentication = (boolean) Mage::getStoreConfig(
        	'channel/global/authentication'
        );
        // check if authentication is requested
        if (!$this->_authentication) {
            return Mage::getModel('api/user');
        }
		// if the user is already logged in, redirect to the requested resource
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return $this->_getSession()->getUser();
        }
        // load the session
        $session = $this->_getSession();
        // get the username from the request
        $username = $this->getRequest()->getServer(
            Faett_Channel_IndexController::PHP_AUTH_USER
        );
        // get the password from the request
        $password = $this->getRequest()->getServer(
            Faett_Channel_IndexController::PHP_AUTH_PW
        );
        // check if username and password is not empty
        if (!empty($username) && !empty($password)) {
            try {
                // login workflow
                return $session->login($username, $password);
            } catch (Exception $e) {
                $message = $e->getMessage();
                Mage::logException($e);
                $session->addError($message);
                $session->setUsername($username);
                // throw an exception
                throw Faett_Channel_Exceptions_InvalidCredentialsException::create(
                	'You\'ve entered invalid user credentials',
                	'200.error.invalid-credentials'
                );
            }
        } else {
            // throw an exception
            throw Faett_Channel_Exceptions_InvalidCredentialsException::create(
            	'Please enter your user credentials',
            	'200.error.no-credentials'
            );
        }
    }

    /**
	 * This method checks that the user logged into the system
	 * is auhtorized to load the requested resource.
	 *
	 * @param array $args
	 * 		The arguments necessary for rendering the resource representation
	 * @return void
     */
    protected function _authorize(array $args = array()) 
    {
    	// load the acutal channel first
    	$channel = Mage::getModel('channel/channel')
    		->load(Mage::app()->getStore()->getId());
        // load the resource name
        $channelName = $channel->getCode();
        // load the resource path
        $resourcePath = $this->_params[Faett_Channel_Helper_Data::TYPE];
        // try to load the resource method (alias for the package name)
        $packageName = '';
        if (array_key_exists(Faett_Channel_Helper_Data::ID, $this->_params)) {
        	$packageName = $this->_params[Faett_Channel_Helper_Data::ID];
        }
        // check if an resource name and a method name can be extracted
        if (empty($packageName)) {
        	if (empty($channelName)) {
	            throw Faett_Channel_Exceptions_NoResourcePathException::create(
	            	'The requested empty resource is not available',
	            	'200.error.acl.no-resource-path'
	            );
	        }
        }
        // load the ACL resource information
        $resources = $this->_getConfig()->getResources();        
        // check if the requested resource exists
        if (!isset($resources->$channelName)) {
            throw Faett_Channel_Exceptions_InvalidResourcePathException::create(
            	'The requested channel ' . $channelName . ' is not available',
                '200.error.acl.invalid-resource-path'
            );
        }  
		// if a resource method (package) was found, check if the method exists
        if (!empty($packageName)) {
	        if (!isset($resources->$channelName->methods->$packageName)) {
	            throw Faett_Channel_Exceptions_InvalidResourcePathException::create(
	            	'The requested package ' . $channelName . '/' . $packageName .  ' is not available',
	                '200.error.acl.invalid-resource-path'
	            );
	        }
        }        
        // check if authentication is set
        if ($channel->hasAuthentication()) {
            // is yes, check if the user is allowed to open the resource
            if (!isset($resources->$channelName->public)
                && isset($resources->$channelName->acl)
                && !$this->_getSession()->isAllowed((string) $resources->$channelName->acl)) {
                throw Faett_Channel_Exceptions_AuthorizationException::create(
                	'You\'ve not the permissions to access the requested resource ' . $channelName,
                	'200.error.acl.access-denied'
                );
            }    
            // AND check if the user is allowed to invoke the resource method
            if (!empty($packageName)) {
	            if (!isset($resources->$channelName->methods->$packageName->public)
	                && isset($resources->$channelName->methods->$packageName->acl)
	                && !$this->_getSession()->isAllowed((string) $resources->$channelName->methods->$packageName->acl)) {
	                throw Faett_Channel_Exceptions_AuthorizationException::create(
	                	'You\'ve not the permissions to access the requested resource ' . $channelName . '/' . $packageName,
	                	'200.error.acl.access-denied'
	                );
	            }
            }
        }
        // invoke the model associated to the ACL
        $modelName = (string) $resources->$channelName->model;
        // instanciate the model
        try {
            $model = Mage::getModel($modelName);
            if ($model instanceof Mage_Api_Model_Resource_Abstract) {
                $model->setResourceConfig($resources->$channelName);
            }
        } catch (Exception $e) {
            throw Faett_Channel_Exceptions_AuthorizationException::create(
            	'The requested resource ' . $channelName .' can not be loaded',
                '200.error.resource-not-callable'
            );
        }
        // split the resource path and the resource method
        list ($resourceName, $resourceMethod) = explode('/', $resourcePath);
        // add the resource method to invoke to the arguments
        array_push($args, $resourceMethod);        
        // load the method information
        if (!empty($packageName)) {
        	// check if a method to invoke is set for the package
	        $methodInfo = $resources->$channelName->methods->$packageName;
	        $method = (isset($methodInfo->method) ? (string) $methodInfo->method : $resourceName);
        } else {
        	// if not, the method to invoke IS the resource name 
        	$method = $resourceName;
        }
        // check if the requested method can be called
        if (is_callable(array(&$model, $method))) {
            if (isset($methodInfo->arguments) && ((string) $methodInfo->arguments) == 'array') {
                return $model->$method((is_array($args) ? $args : array($args)));
            } elseif (!is_array($args)) {
                return $model->$method($args);
            } else {
                return call_user_func_array(array(&$model, $method), $args);
            }
        } else {
            throw Faett_Channel_Exceptions_AuthorizationException::create(
            	'The requested resource ' . $channelName . '/' . $packageName . ' can not be loaded',
                '200.error.resource-method-not-callable'
            );
        }
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('channel/session');
    }

    /**
     * Retrive webservice configuration
     *
     * @return Mage_Api_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('api/config');
    }

	/**
	 * Builds and adds the channel.xml content to the
	 * response.
	 *
	 * @return void
	 */
	public function indexAction()
	{
		// load the channel
		$channel = Mage::getModel('channel/channel')->load(
			Mage::app()->getStore()->getId()
		);
        // send the XML information with the response
        Mage::register(Faett_Channel_Model_Channel::REGISTRY, $channel);
        // load and render the layout
		$this->loadLayout(false);
    	// set content type
        $this->getResponse()->setHeader(
        	'Content-type', $this->_params[Faett_Channel_Helper_Data::CONTENT_TYPE]
        );
        // set the content length (IMPORTANT to avoid Header 'Transfer-Encoding: chunked')
        $this->getResponse()->setHeader('Content-Length', strlen($this->getLayout()->getOutput()));
        // render the layout
		$this->renderLayout();
	}

	/**
	 * This action handles all category ressource requests by
	 * resolving the parameters from the ressource URI, renders
	 * and returns the requested ressource representation.
	 *
	 * @return void
	 */
	public function cAction()
	{
		try {
            // user authentication
            $user = $this->_authenticate();
            // load the category model
    		$category = Mage::getModel(Faett_Channel_Model_Category::MODEL);
            // try to load the if of the requested category
    		if (array_key_exists(Faett_Channel_Helper_Data::ID, $this->_params)) {
    		    $category->setValue(
    		        $category->loadByValue(
    		            $this->_params[Faett_Channel_Helper_Data::ID]
    		        )
    		    );
    		}
            // try to load the requested version and register it in the registry (necessary for block caching)
    		if (array_key_exists(Faett_Channel_Helper_Data::TYPE, $this->_params)) {
    			Mage::register(
    				Faett_Channel_Helper_Data::TYPE, 
    				$this->_params[Faett_Channel_Helper_Data::TYPE]
    			);
    		}
            // check the ACL's and render the resource
    		$this->_authorize(array($user, $category));
            // register the category
            Mage::register(Faett_Channel_Model_Category::REGISTRY, $category);
            // load and render the layout
            $this->loadLayout(false);
    		// set content type
            $this->getResponse()->setHeader(
            	'Content-type', $this->_params[Faett_Channel_Helper_Data::CONTENT_TYPE]
            );
        	// set the content length (IMPORTANT to avoid Header 'Transfer-Encoding: chunked')
        	$this->getResponse()->setHeader('Content-Length', strlen($this->getLayout()->getOutput()));
            // render the layout
        	$this->renderLayout();
		} catch(Faett_Channel_Exceptions_InvalidCredentialsException $ice) {
	        // log the exception
		    Mage::logException($ice);
		    // send an unauthorized error
		    Mage::register(
				Faett_Channel_Block_Unauthorized::MESSAGE, 
			    $this->_getHelper()->__($ice->getKey())
		    );
			// forward to the unauthorized page
		    $this->_forward('unauthorized', 'error', 'channel');
		} catch(Faett_Channel_Exceptions_Interfaces_AuthorizationException $ae) {
	        // log the exception
		    Mage::logException($ae);
			// send a forbidden error
			Mage::register(
				Faett_Channel_Block_Forbidden::MESSAGE,
				$this->_getHelper()->__($ae->getKey())
			);
			// forward to the forbidden page
		    $this->_forward('forbidden', 'error', 'channel');
		} catch(Exception $e) {
	        // log the exception
		    Mage::logException($e);
		    // register the error message
		    Mage::register(
		    	Faett_Channel_Block_InternalServerError::MESSAGE,
		    	$e->getMessage()
		    );
			// forward to the internal server error page
		    $this->_forward('internalServerError', 'error', 'channel');
		}
	}

	/**
	 * This action handles all maintainer ressource requests by
	 * resolving the parameters from the ressource URI, renders
	 * and returns the requested ressource representation.
	 *
	 * @return void
	 */
	public function mAction()
	{
		try {
            // user authentication
            $user = $this->_authenticate();
            // load the maintainer model
    		$maintainer = Mage::getModel(Faett_Channel_Model_Maintainer::MODEL);
            // try to load the if of the requested maintainer
    		if (array_key_exists(Faett_Channel_Helper_Data::ID, $this->_params)) {
    		    $maintainer->loadByUsername(
    		        $this->_params[Faett_Channel_Helper_Data::ID]
    		    );
    		}
            // try to load the requested version and register it in the registry (necessary for block caching)
    		if (array_key_exists(Faett_Channel_Helper_Data::TYPE, $this->_params)) {
    			Mage::register(
    				Faett_Channel_Helper_Data::TYPE, 
    				$this->_params[Faett_Channel_Helper_Data::TYPE]
    			);
    		}
            // check the ACL's and render the resource
    		$this->_authorize(array($user, $maintainer));
            // register the maintainer
            Mage::register(Faett_Channel_Model_Maintainer::REGISTRY, $maintainer);
            // load and render the layout
            $this->loadLayout(false);
    		// set content type
            $this->getResponse()->setHeader(
            	'Content-type', $this->_params[Faett_Channel_Helper_Data::CONTENT_TYPE]
            );
        	// set the content length (IMPORTANT to avoid Header 'Transfer-Encoding: chunked')
        	$this->getResponse()->setHeader('Content-Length', strlen($this->getLayout()->getOutput()));
            // render the layout
            $this->renderLayout();
		} catch(Faett_Channel_Exceptions_InvalidCredentialsException $ice) {
	        // log the exception
		    Mage::logException($ice);
		    // send an unauthorized error
		    Mage::register(
				Faett_Channel_Block_Unauthorized::MESSAGE, 
			    $this->_getHelper()->__($ice->getKey())
		    );
			// forward to the unauthorized page
		    $this->_forward('unauthorized', 'error', 'channel');
		} catch(Faett_Channel_Exceptions_Interfaces_AuthorizationException $ae) {
	        // log the exception
		    Mage::logException($ae);
			// send a forbidden error
			Mage::register(
				Faett_Channel_Block_Forbidden::MESSAGE,
				$this->_getHelper()->__($ae->getKey())
			);
			// forward to the forbidden page
		    $this->_forward('forbidden', 'error', 'channel');
		} catch(Exception $e) {
	        // log the exception
		    Mage::logException($e);
		    // register the error message
		    Mage::register(
		    	Faett_Channel_Block_InternalServerError::MESSAGE,
		    	$e->getMessage()
		    );
			// forward to the internal server error page
		    $this->_forward('internalServerError', 'error', 'channel');
		}
	}

	/**
	 * This action handles all package ressource requests by
	 * resolving the parameters from the ressource URI, renders
	 * and returns the requested ressource representation.
	 *
	 * @return void
	 */
	public function pAction()
	{
		try {
            // user authentication
            $user = $this->_authenticate();
            // load the package model
    		$package = Mage::getModel(Faett_Channel_Model_Package::MODEL);
            // try to load the if of the requested package
    		if (array_key_exists(Faett_Channel_Helper_Data::ID, $this->_params)) {
    		    $package->loadByPackageName(
    		        $this->_params[Faett_Channel_Helper_Data::ID]
    		    );
    		}
            // try to load the requested version and register it in the registry (necessary for block caching)
    		if (array_key_exists(Faett_Channel_Helper_Data::TYPE, $this->_params)) {
    			Mage::register(
    				Faett_Channel_Helper_Data::TYPE, 
    				$this->_params[Faett_Channel_Helper_Data::TYPE]
    			);
    		}
            // check the ACL's and render the resource
    		$this->_authorize(array($user, $package));
            // register the package
            Mage::register(Faett_Channel_Model_Package::REGISTRY, $package);            
            // load and render the layout
            $this->loadLayout(false);
    		// set content type
            $this->getResponse()->setHeader(
            	'Content-type', $this->_params[Faett_Channel_Helper_Data::CONTENT_TYPE]
            );
        	// set the content length (IMPORTANT to avoid Header 'Transfer-Encoding: chunked')
        	$this->getResponse()->setHeader('Content-Length', strlen($this->getLayout()->getOutput()));
            // render the layout
            $this->renderLayout();
		} catch(Faett_Channel_Exceptions_InvalidCredentialsException $ice) {
	        // log the exception
		    Mage::logException($ice);
		    // send an unauthorized error
		    Mage::register(
				Faett_Channel_Block_Unauthorized::MESSAGE, 
			    $this->_getHelper()->__($ice->getKey())
		    );
			// forward to the unauthorized page
		    $this->_forward('unauthorized', 'error', 'channel');
		} catch(Faett_Channel_Exceptions_Interfaces_AuthorizationException $ae) {
	        // log the exception
		    Mage::logException($ae);
			// send a forbidden error
			Mage::register(
				Faett_Channel_Block_Forbidden::MESSAGE,
				$this->_getHelper()->__($ae->getKey())
			);
			// forward to the forbidden page
		    $this->_forward('forbidden', 'error', 'channel');
		} catch(Exception $e) {
	        // log the exception
		    Mage::logException($e);
		    // register the error message
		    Mage::register(
		    	Faett_Channel_Block_InternalServerError::MESSAGE,
		    	$e->getMessage()
		    );
			// forward to the internal server error page
		    $this->_forward('internalServerError', 'error', 'channel');
		}
	}

	/**
	 * This action handles all release ressource requests by
	 * resolving the parameters from the ressource URI, renders
	 * and returns the requested ressource representation.
	 *
	 * @return void
	 */
	public function rAction()
	{
		try {
            // user authentication
            $user = $this->_authenticate();
            // load the package model
    		$package = Mage::getModel(Faett_Channel_Model_Package::MODEL);
            // try to load the if of the requested release
    		if (array_key_exists(Faett_Channel_Helper_Data::ID, $this->_params)) {
    		    $package->loadByPackageName(
    		        $this->_params[Faett_Channel_Helper_Data::ID]
    		    );
    		}
            // try to load the requested version
    		if (array_key_exists(Faett_Channel_Helper_Data::VERSION, $this->_params)) {
    		    $package->setVersion(
    		        $version = $this->_params[Faett_Channel_Helper_Data::VERSION]
    		    );
    		}
            // try to load the requested version and register it in the registry (necessary for block caching)
    		if (array_key_exists(Faett_Channel_Helper_Data::TYPE, $this->_params)) {
    			Mage::register(
    				Faett_Channel_Helper_Data::TYPE, 
    				$this->_params[Faett_Channel_Helper_Data::TYPE]
    			);
    		}
            // check the ACL's and render the resource
    		$this->_authorize(array($user, $package));
            // register the package
            Mage::register(Faett_Channel_Model_Package::REGISTRY, $package);
            // load and render the layout
            $this->loadLayout(false);
    		// set content type
            $this->getResponse()->setHeader(
            	'Content-type', $this->_params[Faett_Channel_Helper_Data::CONTENT_TYPE]
            );
        	// set the content length (IMPORTANT to avoid Header 'Transfer-Encoding: chunked')
        	$this->getResponse()->setHeader('Content-Length', strlen($this->getLayout()->getOutput()));
            // render the layout
            $this->renderLayout();
		} catch(Faett_Channel_Exceptions_InvalidCredentialsException $ice) {
	        // log the exception
		    Mage::logException($ice);
		    // send an unauthorized error
		    Mage::register(
				Faett_Channel_Block_Unauthorized::MESSAGE, 
			    $this->_getHelper()->__($ice->getKey())
		    );
			// forward to the unauthorized page
		    $this->_forward('unauthorized', 'error', 'channel');
		} catch(Faett_Channel_Exceptions_Interfaces_AuthorizationException $ae) {
	        // log the exception
		    Mage::logException($ae);
			// send a forbidden error
			Mage::register(
				Faett_Channel_Block_Forbidden::MESSAGE,
				$this->_getHelper()->__($ae->getKey())
			);
			// forward to the forbidden page
		    $this->_forward('forbidden', 'error', 'channel');
		} catch(Exception $e) {
	        // log the exception
		    Mage::logException($e);
		    // register the error message
		    Mage::register(
		    	Faett_Channel_Block_InternalServerError::MESSAGE,
		    	$e->getMessage()
		    );
			// forward to the internal server error page
		    $this->_forward('internalServerError', 'error', 'channel');
		}
	}
}