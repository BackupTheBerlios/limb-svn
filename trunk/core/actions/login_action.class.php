<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: login_action.class.php 401 2004-02-04 15:40:14Z server $
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
			reload('/');
			
		return $is_logged;
	}
}

?>