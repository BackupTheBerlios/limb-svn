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
require_once(LIMB_DIR . 'class/core/actions/form_create_site_object_action.class.php');

class create_documents_folder_action extends form_create_site_object_action
{
	protected function _define_site_object_class_name()
	{
	  return 'documents_folder';
	}  
	  
	protected function _define_dataspace_name()
	{
	  return 'create_documents_folder';
	}
  
	protected function _init_validator()
	{
		parent :: _init_validator();
    $this->validator->add_rule($v = array(LIMB_DIR . 'class/validators/rules/required_rule', 'title'));
	}
}

?>