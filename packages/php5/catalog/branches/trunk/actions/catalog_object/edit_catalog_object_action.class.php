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

class edit_catalog_object_action extends form_edit_site_object_action
{
	protected function _define_site_object_class_name()
	{
	  return 'catalog_object';
	}  
	  
	protected function _define_dataspace_name()
	{
	  return 'catalog_object_form';
	}
  
  protected function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'annotation' => 'annotation',
  				'object_content' => 'content',
  				'image_id' => 'image_id'
	      )
	  );     
	}  
	
	protected function _init_validator()
	{
		parent :: _init_validator();

    $this->validator->add_rule($v1 = array(LIMB_DIR . 'class/validators/rules/required_rule', 'annotation'));
    $this->validator->add_rule($v2 = array(LIMB_DIR . 'class/validators/rules/required_rule', 'object_content'));
	}
}

?>