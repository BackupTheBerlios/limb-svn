<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_document_action.class.php 786 2004-10-12 14:24:43Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/form_edit_site_object_command.class.php');

class edit_document_form_command extends form_edit_site_object_command
{
  protected function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'document_content' => 'content',
  				'annotation' => 'annotation',
	      )
	  );     
	}  
	
	protected function _register_validation_rules($validator, $dataspace)
	{
    parent :: _register_validation_rules($validator, $dataspace);
    
    $validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'title'));
    $validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'document_content'));
	}
}

?>