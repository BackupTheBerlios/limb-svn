<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: change_password_action.class.php 470 2004-02-18 13:04:56Z mike $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_edit_site_object_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/match_rule.class.php');

class change_password_action extends form_edit_site_object_action
{

	var $definition = array(
			'site_object' => 'user_object',
			'datamap' => array(
				'identifier' => 'identifier',
				'password' => 'password',
			)
	);

	function change_password_action($name = 'change_password')
	{
		
		parent :: form_edit_site_object_action($name);
	}

	
	function _init_validator()
	{
		$this->validator->add_rule(new required_rule('password'));
		$this->validator->add_rule(new required_rule('second_password'));
		$this->validator->add_rule(new match_rule('second_password', 'password', 'PASSWORD'));
	}
	
	function _update_object_operation(&$object)
	{
		trigger_error("Stop",E_USER_WARNING);	
		if(!$object->change_password())
			return false;
		else
			return true;
	}
}

?>