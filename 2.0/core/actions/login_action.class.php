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

class login_action extends form_action
{
	function login_action($name='login_form')
	{
		parent :: form_action($name);
	}

	function _init_validator()
	{
		$this->validator->add_rule(new required_rule('login'));
		$this->validator->add_rule(new required_rule('password'));
	}
	
	function _valid_perform()
	{
		$login = $this->_get('login');
		$password = $this->_get('password');
		
		$is_logged = user :: login($login, $password);
		
		if($is_logged)
		{
			$this->_process_logged_user();
			reload('/');
		}
		else
			$this->_process_not_logged_user();
			
		return $is_logged;
	}

	function _process_logged_user()
	{
	}

	function _process_not_logged_user()
	{
	}

}

?>