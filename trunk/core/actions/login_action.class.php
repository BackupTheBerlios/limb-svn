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

	function _define_dataspace_name()
	{
	  return 'login_form';
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
		$login = $this->dataspace->get('login');
		$password = $this->dataspace->get('password');
		$locale_id = $this->dataspace->get('locale_id');
		
		$user_object =& site_object_factory :: create($this->user_object_class_name);

		if($user_object->login($login, $password, $locale_id))
		{
			if($redirect = $this->dataspace->get('redirect'))
				return $this->_login_redirect($redirect);
			elseif(isset($_SERVER['HTTP_REFERER']) && strpos(strtolower($_SERVER['HTTP_REFERER']), '/root/login') === false)
				return new redirect_response(RESPONSE_STATUS_FORM_SUBMITTED, $_SERVER['HTTP_REFERER']);
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