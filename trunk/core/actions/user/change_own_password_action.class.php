<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: change_own_password_action.class.php 470 2004-02-18 13:04:56Z mike $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/match_rule.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/required_rule.class.php'); 

class change_own_password_action extends form_action
{
	function change_own_password_action($name = 'change_own_password')
	{
		parent :: form_action($name);
	}

	
	function _init_validator()
	{
		$this->validator->add_rule(new required_rule('password'));
		$this->validator->add_rule(new required_rule('second_password'));
		$this->validator->add_rule(new match_rule('second_password', 'password', 'PASSWORD'));
	}
	

	function _valid_perform()
	{
		$user_object =& site_object_factory :: instance('user_object');
		
		$data = $this->dataspace->export();

		if (!$user_object->validate_password($data['old_password']))
		{
			$error_list = & error_list :: instance();
			$error_list->add_error('old_password', 'WRONG_PASSWORD');
			$this->valid = false;
			return $this->valid;
		}

		return $user_object->change_own_password($data['password']);
	}

}

?>