<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_subscribe_theme_action.class.php 245 2004-03-05 12:11:42Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');

class create_subscribe_theme_action extends form_create_site_object_action
{
	function _define_site_object_class_name()
	{
	  return 'subscribe_theme';
	}  
	  
	function _define_dataspace_name()
	{
	  return 'create_subscribe_theme';
	}
  
  function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'mail_template' => 'mail_template',
	      )
	  );     
	}  
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('title'));
		$this->validator->add_rule(new required_rule('mail_template'));
	}
}

?>