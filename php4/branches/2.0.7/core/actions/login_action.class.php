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
require_once(LIMB_DIR . 'core/lib/validators/rules/required_rule.class.php');
require_once(LIMB_DIR . 'core/actions/form_action.class.php');
require_once(LIMB_DIR . 'core/model/response/redirect_response.class.php');

class login_action extends form_action
{
	var $user_object_class_name = 'user_object';

	function login_action($name='login_form')
	{
		parent :: form_action($name);
	}

	function _init_validator()
	{
		$this->validator->add_rule(new required_rule('login'));
		$this->validator->add_rule(new required_rule('password'));
	}
	
	function _init_dataspace()
	{
		parent :: _init_dataspace();
		
		$this->_transfer_redirect_param();
	}	
	
	function _transfer_redirect_param()
	{
		if (!isset($_REQUEST['redirect']) || !$_REQUEST['redirect'])
			return ;

		$this->dataspace->set('redirect', urldecode($this->_get_redirect_string()));
	}
	
	function _get_redirect_string()
	{
		if(!isset($_REQUEST['redirect']))
			return '';
			
		$redirect = $_REQUEST['redirect'];
		
		if(!preg_match("/^([a-z0-9\.#\/\?&=\+\-_]+)/si", $redirect))
			return '';
				
		return $redirect;
	}
	
	function _valid_perform()
	{
		$login = $this->_get('login');
		$password = $this->_get('password');
		
		$user_object =& site_object_factory :: create($this->user_object_class_name);

		if($user_object->login($login, $password))
		{
			if($redirect = $this->_get('redirect'))
				return $this->_login_redirect($redirect);
			else
				return new redirect_response(RESPONSE_STATUS_FORM_SUBMITTED, '/');
		}
		else	
			return new failed_response();
	}
	
	function _login_redirect($redirect)
	{
		return new redirect_response(RESPONSE_STATUS_FORM_SUBMITTED, $redirect);
	}
}

?>