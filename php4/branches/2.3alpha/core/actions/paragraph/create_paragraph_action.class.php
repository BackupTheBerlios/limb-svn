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
require_once(LIMB_DIR . '/core/actions/form_create_site_object_action.class.php');

class create_paragraph_action extends form_create_site_object_action
{
	function _define_site_object_class_name()
	{
	  return 'paragraph';
	}  
	  
	function _define_dataspace_name()
	{
	  return 'paragraph_form';
	}
  
  function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'paragraph_content' => 'content',
	      )
	  );     
	}  

	function _init_validator()
	{
		parent :: _init_validator();
		
    $this->validator->add_rule($v = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'paragraph_content'));
	}
}

?>