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

class edit_article_action extends form_edit_site_object_action
{
	protected function _define_site_object_class_name()
	{
	  return 'article';
	}  
	  
	protected function _define_dataspace_name()
	{
	  return 'article_form';
	}
  
  protected function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'article_content' => 'content',
  				'annotation' => 'annotation',
  				'author' => 'author',
  				'source' => 'source',
  				'uri' => 'uri',
	      )
	  );     
	}  
	
	protected function _init_validator()
	{
		parent :: _init_validator();

    $this->validator->add_rule($v1 = array(LIMB_DIR . 'class/validators/rules/required_rule', 'title'));
    $this->validator->add_rule($v2 = array(LIMB_DIR . 'class/validators/rules/required_rule', 'author'));
    $this->validator->add_rule($v3 = array(LIMB_DIR . 'class/validators/rules/required_rule', 'article_content'));
	}
}

?>