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
require_once(LIMB_DIR . 'class/core/actions/form_create_site_object_action.class.php');

class create_user_action extends form_create_site_object_action
{
	protected function _define_site_object_class_name()
	{
	  return 'user_object';
	}  
	  
	protected function _define_dataspace_name()
	{
	  return 'create_user';
	}
  
  protected function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'name' => 'name',
  				'lastname' => 'lastname',
  				'password' => 'password',
  				'email' => 'email',
  				'second_password' => 'second_password',
	      )
	  );     
	}  
	
	protected function _init_validator()
	{
		parent :: _init_validator();
		
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/unique_user_rule', 'identifier'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/unique_user_email_rule', 'email'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'name'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/email_rule', 'email'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'password'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'second_password'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/match_rule', 'second_password', 'password', 'PASSWORD'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'email'));
	}
}

?>