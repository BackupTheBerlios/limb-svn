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
require_once(LIMB_DIR . 'class/core/actions/form_edit_site_object_action.class.php');

class change_password_action extends form_edit_site_object_action
{
	function _define_site_object_class_name()
	{
	  return 'user_object';
	}  
	  
	function _define_dataspace_name()
	{
	  return 'change_password';
	}
  
  function _define_datamap()
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

	function _init_validator()
	{
    $this->validator->add_rule($v1 = array(LIMB_DIR . 'class/validators/rules/required_rule', 'password'));
    $this->validator->add_rule($v2 = array(LIMB_DIR . 'class/validators/rules/required_rule', 'second_password'));
    $this->validator->add_rule($v3 = array(LIMB_DIR . 'class/validators/rules/match_rule', 'second_password', 'password', 'PASSWORD'));
	}
	
	function perform(&$request, &$response)
	{
	  parent :: perform($request, $response);
	  
		if (!$this->_changing_own_password())
		  return;

		if (REQUEST_STATUS_SUCCESS == $request->get_status())
		{			  
  		if($request->has_attribute('popup'))
  		  $response->write(close_popup_response($request, '/'));
		}
	}
	
	function _changing_own_password()
	{
		$object_data = $this->_load_object_data();
		
		$user =& user :: instance();
		
		return ($object_data['id'] == $user->get_id()) ? true : false;
	}
	
	function _update_object_operation()
	{
		return $this->object->change_password();
	}
}

?>