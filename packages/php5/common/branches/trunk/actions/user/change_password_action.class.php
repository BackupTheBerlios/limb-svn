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
require_once(LIMB_DIR . 'class/core/user.class.php');
require_once(LIMB_DIR . 'class/core/actions/form_edit_site_object_action.class.php');

class change_password_action extends form_edit_site_object_action
{
	protected function _define_site_object_class_name()
	{
	  return 'user_object';
	}  
	  
	protected function _define_dataspace_name()
	{
	  return 'change_password';
	}
  
  protected function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'identifier' => 'identifier',
  				'password' => 'password',
  				'second_password' => 'second_password',
	      )
	  );     
	}  

	protected function _init_validator()
	{
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'password'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'second_password'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/match_rule', 'second_password', 'password', 'PASSWORD'));
	}
	
	public function _valid_perform($request, $response)
	{
	  parent :: _valid_perform($request, $response);
	  
		if ($this->_changing_own_password())
		{
			user :: instance()->logout();
			message_box :: write_warning(strings :: get('need_relogin', 'user'));
		}
		else
		{
  		$object_data = $this->_load_object_data();
		  session :: destroy_user_session($object_data['id']);
		}  

		if ($request->get_status() == request :: STATUS_SUCCESS)
		{			  
  		if($request->has_attribute('popup'))
  		  $response->write(close_popup_response($request, '/'));
		}
	}
	
	protected function _changing_own_password()
	{
		$object_data = $this->_load_object_data();
		
		return ($object_data['id'] == user :: instance()->get_id()) ? true : false;
	}
	
	protected function _update_object_operation()
	{
		$this->object->change_password();
	}
}

?>