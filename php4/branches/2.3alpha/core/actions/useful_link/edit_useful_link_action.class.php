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
require_once(LIMB_DIR . '/core/actions/form_edit_site_object_action.class.php');

class edit_useful_link_action extends form_edit_site_object_action
{
	function _define_site_object_class_name()
	{
	  return 'useful_link';
	}  
	  
	function _define_dataspace_name()
	{
	  return 'useful_link_form';
	}
  
  function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'annotation' => 'annotation',
  				'image_id' => 'image_id',
  				'uri' => 'uri',
	      )
	  );     
	}  
	
	function _init_validator()
	{
		parent :: _init_validator();

    $this->validator->add_rule($v1 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'annotation'));
    $this->validator->add_rule($v2 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'uri'));
    $this->validator->add_rule($v3 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'title'));
    $this->validator->add_rule($v4 = array(LIMB_DIR . '/core/lib/validators/rules/url_rule', 'uri'));
	}
}

?>