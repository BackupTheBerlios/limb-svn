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

class edit_user_action extends form_edit_site_object_action
{
	function _define_site_object_class_name()
	{
	  return 'user_object';
	}  
	  
	function _define_dataspace_name()
	{
	  return 'edit_user';
	}
  
  function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'name' => 'name',
  				'lastname' => 'lastname',
  				'email' => 'email',
	      )
	  );     
	}  

	function _define_increase_version_flag()
	{
	  return false;
	}  
	
	function _init_validator()
	{
		parent :: _init_validator();
		
		if ($object_data =& fetch_requested_object())
		{
      $this->validator->add_rule($v1 = array(LIMB_DIR . 'core/lib/validators/rules/unique_user_rule', 'identifier', $object_data['identifier']));
      $this->validator->add_rule($v2 = array(LIMB_DIR . 'core/lib/validators/rules/unique_user_email_rule', 'email', $object_data['email']));
		}	

    $this->validator->add_rule($v3 = array(LIMB_DIR . 'core/lib/validators/rules/required_rule', 'name'));
    $this->validator->add_rule($v4 = array(LIMB_DIR . 'core/lib/validators/rules/required_rule', 'email'));
    $this->validator->add_rule($v5 = array(LIMB_DIR . 'core/lib/validators/rules/email_rule', 'email'));
	}
}

?>