<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'class/core/actions/form_action.class.php');

class login_action extends form_action
{
	protected $user_object_class_name = 'user_object';

	protected function _define_dataspace_name()
	{
	  return 'login_form';
	}

	protected function _init_validator()
	{
		$this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'login'));
		$this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'password'));
	}
	
	protected function _init_dataspace($request)
	{
		parent :: _init_dataspace($request);
		
		$this->_transfer_redirect_param($request);
	}	
	
	protected function _transfer_redirect_param($request)
	{
		if(!$redirect = $request->get('redirect'))
			return;

		$this->dataspace->set('redirect', urldecode($this->_get_redirect_string($request)));
	}
	
	protected function _get_redirect_string($request)
	{
		if(!$redirect = $request->get('redirect'))
			return '';
			
		if(!preg_match("/^([a-z0-9\.#\/\?&=\+\-_]+)/si", $redirect))
			return '';
				
		return $redirect;
	}
	
	protected function _valid_perform($request, $response)
	{
		$login = $this->dataspace->get('login');
		$password = $this->dataspace->get('password');
		$locale_id = $this->dataspace->get('locale_id');
		$autologin = $this->dataspace->get('autologin');

		$user_object = site_object_factory :: create($this->user_object_class_name);

		if($user_object->login($login, $password, $locale_id))
		{
  		if($autologin)
  		{
  			user :: instance()->$user->configure_autologin();
  		}
  			
  		$request->set_status(request :: STATUS_FORM_SUBMITTED);
		  
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
		
		$request->set_status(request :: STATUS_FAILURE);
	}
	
	protected function _login_redirect($redirect, $response)
	{
		$response->redirect($redirect);
	}
}

?>