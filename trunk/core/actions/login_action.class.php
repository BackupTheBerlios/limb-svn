<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_action.class.php');

class login_action extends form_action
{
	var $user_object_class_name = 'user_object';

	function _define_dataspace_name()
	{
	  return 'login_form';
	}

	function _init_validator()
	{
		$this->validator->add_rule($v1 = array(LIMB_DIR . 'core/lib/validators/rules/required_rule', 'login'));
		$this->validator->add_rule($v2 = array(LIMB_DIR . 'core/lib/validators/rules/required_rule', 'password'));
	}
	
	function _init_dataspace(&$request)
	{
		parent :: _init_dataspace($request);
		
		$this->_transfer_redirect_param($request);
	}	
	
	function _transfer_redirect_param(&$request)
	{
		if(!$redirect = $request->get_attribute('redirect'))
			return;

		$this->dataspace->set('redirect', urldecode($this->_get_redirect_string($request)));
	}
	
	function _get_redirect_string(&$request)
	{
		if(!$redirect = $request->get_attribute('redirect'))
			return '';
			
		if(!preg_match("/^([a-z0-9\.#\/\?&=\+\-_]+)/si", $redirect))
			return '';
				
		return $redirect;
	}
	
	function _valid_perform(&$request, &$response)
	{
		$login = $this->dataspace->get('login');
		$password = $this->dataspace->get('password');
		$locale_id = $this->dataspace->get('locale_id');
		
		$user_object =& site_object_factory :: create($this->user_object_class_name);

		if($user_object->login($login, $password, $locale_id))
		{
  		$request->set_status(REQUEST_STATUS_FORM_SUBMITTED);
		  
			if($redirect = $this->dataspace->get('redirect'))
			{
			  $this->_login_redirect($redirect, $response);
				return;
			}
			elseif(isset($_SERVER['HTTP_REFERER']) && strpos(strtolower($_SERVER['HTTP_REFERER']), '/root/login') === false)
			{
    		$response->redirect($_SERVER['HTTP_REFERER']);
    		return;
			}	
			else
			{
    		$response->redirect('/');
    		return;
			}	
		}
		
		$request->set_status(REQUEST_STATUS_FAILURE);
	}
	
	function _login_redirect($redirect, &$response)
	{
		$response->redirect($redirect);
	}
}

?>