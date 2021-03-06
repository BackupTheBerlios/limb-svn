<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_subscribe_mail_action.class.php 245 2004-03-05 12:11:42Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/actions/form_create_site_object_action.class.php');

class create_subscribe_mail_action extends form_create_site_object_action
{
	function _define_site_object_class_name()
	{
	  return 'subscribe_mail';
	}  
	  
	function _define_dataspace_name()
	{
	  return 'create_subscribe_mail';
	}
  
  function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'subscribe_mail_content' => 'content',
  				'author' => 'author',
	      )
	  );     
	}   
	
	function _init_validator()
	{
		parent :: _init_validator();

    $this->validator->add_rule($v1 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'title'));
    $this->validator->add_rule($v2 = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'subscribe_mail_content'));
	}

	function _init_dataspace(&$request)
	{
		parent :: _init_dataspace($request);
		
		$parent_object_data =& fetch_requested_object($request);
		
		$data['subscribe_mail_content'] = $parent_object_data['mail_template'];
		
		$this->dataspace->import($data);
	}

}

?>