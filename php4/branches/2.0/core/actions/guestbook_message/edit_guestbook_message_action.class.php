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
require_once(LIMB_DIR . 'core/lib/validators/rules/email_rule.class.php');

class edit_guestbook_message_action extends form_edit_site_object_action
{
	function edit_guestbook_message_action()
	{
		$definition = array(
			'site_object' => 'guestbook_message',
			'datamap' => array(
				'message' => 'message',
				'sender' => 'sender',
				'sender_email' => 'sender_email',
				'comment' => 'comment',
				'comment_author' => 'comment_author',
				'comment_author_email' => 'comment_author_email',
				
			)
		);

		parent :: form_edit_site_object_action('edit_guestbook_message', $definition);
	}
	

	function _init_validator()
	{
		parent :: _init_validator();
		
		$this->validator->add_rule(new required_rule('message'));
		$this->validator->add_rule(new required_rule('sender'));
		$this->validator->add_rule(new email_rule('sender_email'));
		$this->validator->add_rule(new email_rule('comment_author_email'));
	}

	function _init_dataspace()
	{
		parent :: _init_dataspace();
		
		$data = $this->_export();
		
		if (empty($data['comment_author']))
			$data['comment_author'] = user :: get_login();

		if (empty($data['comment_author_email']))
			$data['comment_author_email'] = user :: get_email();
		
		$this->_import($data);
	}
	
	function _process_transfered_dataspace()
	{	
		$this->_htmlspecialchars_dataspace_value('message');
		$this->_htmlspecialchars_dataspace_value('sender_email');
		$this->_htmlspecialchars_dataspace_value('title');
		$this->_htmlspecialchars_dataspace_value('sender');
		$this->_htmlspecialchars_dataspace_value('comment_author');
		$this->_htmlspecialchars_dataspace_value('comment_author_email');
	}
}

?>