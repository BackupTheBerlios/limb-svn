<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'class/core/permissions/user.class.php');
require_once(LIMB_DIR . 'class/core/actions/form_create_site_object_action.class.php');

class create_guestbook_message_action extends form_create_site_object_action
{
	protected function _define_site_object_class_name()
	{
	  return 'guestbook_message';
	}  
	  
	protected function _define_dataspace_name()
	{
	  return 'create_guestbook_message';
	}
  
  protected function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'message' => 'message',
  				'sender' => 'sender',
  				'sender_email' => 'sender_email',
	      )
	  );     
	}  

	protected function _init_validator()
	{
		parent :: _init_validator();

    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'message'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'sender'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/email_rule', 'sender_email'));
	}

	protected function _init_dataspace($request)
	{
		$data['identifier'] = md5(rand());
		
		$user = LimbToolsBox :: getToolkit()->getUser();
		
		$data['sender'] = $user->get_login();
		$data['sender_email'] = $user->get('email', '');
		
		$this->dataspace->import($data);
	}
	
	protected function _process_transfered_dataspace()
	{	
		$this->_htmlspecialchars_dataspace_value('message');
		$this->_htmlspecialchars_dataspace_value('sender_email');
		$this->_htmlspecialchars_dataspace_value('title');
		$this->_htmlspecialchars_dataspace_value('sender');
	}
	
}

?>