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
require_once(LIMB_DIR . 'core/lib/validators/rules/match_rule.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/required_rule.class.php'); 
require_once(LIMB_DIR . 'core/lib/validators/rules/user_old_password_rule.class.php'); 

class change_own_password_action extends form_action
{
	function _define_dataspace_name()
	{
	  return 'change_own_password';
	}

	function _init_validator()
	{
		$this->validator->add_rule(new user_old_password_rule('old_password'));
		$this->validator->add_rule(new required_rule('password'));
		$this->validator->add_rule(new required_rule('second_password'));
		$this->validator->add_rule(new match_rule('second_password', 'password', 'PASSWORD'));
	}

	function _valid_perform()
	{
		$user_object =& site_object_factory :: instance('user_object');
		
		$data = $this->dataspace->export();

		if($user_object->change_own_password($data['password']))
			return new response(RESPONSE_STATUS_FORM_SUBMITTED);
		else
			return new failed_response();
	}

}

?>