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
require_once(LIMB_DIR . 'core/actions/form_site_object_action.class.php');

class login_action extends form_site_object_action
{
	var $definition = array(
		'site_object' => 'user_object',
	);

	function login_action($name='login_form')
	{
		parent :: form_site_object_action($name);
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
		
		$user_object =& $this->get_site_object();
		$is_logged = $user_object->login($login, $password);
		
		if($is_logged)
			reload('/');
			
		return $is_logged;
	}
}

?>