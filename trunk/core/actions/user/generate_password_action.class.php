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
require_once(LIMB_DIR . 'core/lib/validators/rules/required_rule.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/email_rule.class.php');

class generate_password_action extends form_action
{
	function generate_password_action($name = 'generate_password')
	{
		parent :: form_action($name);
	}
	
	function _init_validator()
	{
		$this->validator->add_rule(new required_rule('email'));
		$this->validator->add_rule(new email_rule('email'));		
	}
	
	function _valid_perform()
	{
		$data = $this->_export();
		$object =& site_object_factory :: create('user_object');
		
		$new_non_crypted_password = '';
		if($object->generate_password($data['email'], $new_non_crypted_password))
			return new response(RESPONSE_STATUS_FORM_SUBMITTED);
		else
			return new failed_response();
	}
}

?>