<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: edit_message_action.class.php 707 2004-09-18 14:43:42Z pachanga $
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/class/core/commands/form_edit_site_object_command.class.php');

class edit_message_form_command extends form_edit_site_object_command
{
  protected function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'content' => 'content',
	      )
	  );     
	}  
	
	protected function _register_validation_rules($validator, $dataspace)
	{
    parent :: _register_validation_rules($validator, $dataspace);

    $validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'title'));
    $validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'content'));
	}
}

?>