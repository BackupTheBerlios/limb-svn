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
require_once(LIMB_DIR . 'core/actions/form_edit_site_object_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/match_rule.class.php');

class change_password_action extends form_edit_site_object_action
{
	function change_password_action($name = 'change_password', $merge_definition=array())
	{
		$definition = array(
			'site_object' => 'user_object',
			'datamap' => array(
				'identifier' => 'identifier',
				'password' => 'password',
				'second_password' => 'second_password',
			)
		);

		parent :: form_edit_site_object_action($name, complex_array :: array_merge($definition, $merge_definition));
	}
	
	function _init_validator()
	{
		$this->validator->add_rule(new required_rule('password'));
		$this->validator->add_rule(new required_rule('second_password'));
		$this->validator->add_rule(new match_rule('second_password', 'password', 'PASSWORD'));
	}
	
	function _update_object_operation()
	{
		if(!$this->object->change_password())
			return false;
		else
			return true;
	}
}

?>