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
require_once(LIMB_DIR . 'class/core/actions//form_create_site_object_action.class.php');
require_once(LIMB_DIR . 'class/validators/rules/locale_date_rule.class.php');

class create_news_action extends form_create_site_object_action
{
	function _define_site_object_class_name()
	{
	  return 'news_object';
	}  
	  
	function _define_dataspace_name()
	{
	  return 'news_form';
	}
  
  function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'annotation' => 'annotation',
  				'news_content' => 'content',
  				'news_date' => 'news_date',
	      )
	  );     
	}  
	
	function _init_validator()
	{
		parent :: _init_validator();

    $this->validator->add_rule($v1 = array(LIMB_DIR . 'class/validators/rules/required_rule', 'title'));
    $this->validator->add_rule($v2 = array(LIMB_DIR . 'class/validators/rules/required_rule', 'annotation'));
    $this->validator->add_rule($v3 = array(LIMB_DIR . 'class/validators/rules/required_rule', 'news_date'));
    $this->validator->add_rule($v4 = array(LIMB_DIR . 'class/validators/rules/locale_date_rule', 'news_date'));
	}
}

?>