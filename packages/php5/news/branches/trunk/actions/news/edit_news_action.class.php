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

class edit_news_action extends form_edit_site_object_action
{
	protected function _define_site_object_class_name()
	{
	  return 'news_object';
	}  
	  
	protected function _define_dataspace_name()
	{
	  return 'news_form';
	}
  
  protected function _define_datamap()
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
	
	protected function _init_validator()
	{
		parent :: _init_validator();
		
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'title'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'annotation'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'news_date'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/locale_date_rule', 'news_date'));
	}
}

?>