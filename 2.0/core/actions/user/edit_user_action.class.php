<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: edit_user_action.class.php 427 2004-02-11 09:03:24Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_edit_site_object_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/email_rule.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/unique_user_rule.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/unique_user_email_rule.class.php');

class edit_user_action extends form_edit_site_object_action
{
	function edit_user_action($name = 'edit_user', $merge_definition=array())
	{
		$definition = array(
			'site_object' => 'user_object',
			'datamap' => array(
				'name' => 'name',
				'lastname' => 'lastname',
				'email' => 'email',
			)
		);
		
		parent :: form_edit_site_object_action($name, complex_array :: array_merge($definition, $merge_definition));
	}
	
	function _init_validator()
	{
		parent :: _init_validator();

		$object_data =& fetch_mapped_by_url();

		$this->validator->add_rule(new unique_user_rule('identifier', $object_data['identifier']));
		$this->validator->add_rule(new unique_user_email_rule('email', $object_data['email']));		
		$this->validator->add_rule(new required_rule('name'));
		$this->validator->add_rule(new required_rule('email'));
		$this->validator->add_rule(new email_rule('email'));
	}
	
	function _update_object_operation(&$object)
	{
		if(!$object->update(false))
			return false;
		else
			return true;
	}
	
}

?>