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
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/email_rule.class.php');

class create_guestbook_message_action extends form_create_site_object_action
{
	function create_guestbook_message_action($name = 'create_guestbook_message')
	{
		$definition = array(
			'site_object' => 'guestbook_message',
			'datamap' => array(
				'message' => 'message',
				'sender' => 'sender',
				'sender_email' => 'sender_email',
			)
		);
		
		parent :: form_create_site_object_action($name, $definition);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('message'));
		$this->validator->add_rule(new required_rule('sender'));
		$this->validator->add_rule(new email_rule('sender_email'));
	}

	function _init_dataspace()
	{
		$data['identifier'] = md5(rand());
		
		$data['sender'] = user :: get_login();
		$data['sender_email'] = user :: get_email();
		
		$this->_import($data);
	}
	
	function _process_transfered_dataspace()
	{	
		$this->_htmlspecialchars_dataspace_value('message');
		$this->_htmlspecialchars_dataspace_value('sender_email');
		$this->_htmlspecialchars_dataspace_value('title');
		$this->_htmlspecialchars_dataspace_value('sender');
	}
	
}

?>