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

class edit_faq_folder_action extends form_edit_site_object_action
{
	protected function _define_site_object_class_name()
	{
	  return 'faq_folder';
	}  
	  
	protected function _define_dataspace_name()
	{
	  return 'edit_faq_folder';
	}

	protected function _init_validator()
	{
		parent :: _init_validator();
		
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'title'));
	}
}

?>