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
require_once(LIMB_DIR . '/class/core/actions/form_edit_site_object_action.class.php');

class edit_feedback_action extends form_edit_site_object_action
{
	protected function _define_site_object_class_name()
	{
	  return 'feedback_object';
	}  
	  
	protected function _define_dataspace_name()
	{
	  return 'feedback_form';
	}
  
  protected function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'content' => 'content',
	      )
	  );     
	}   
	
	protected function _init_validator()
	{
		parent :: _init_validator();

    $this->validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'title'));
    $this->validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'content'));
	}
}

?>