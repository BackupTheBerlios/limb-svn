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

class create_faq_object_action extends form_create_site_object_action
{
	protected function _define_site_object_class_name()
	{
	  return 'faq_object';
	}  
	  
	protected function _define_dataspace_name()
	{
	  return 'create_faq_object';
	}
  
  protected function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'question' => 'question',
  				'question_author' => 'question_author',
  				'question_author_email' => 'question_author_email',
  				'answer' => 'answer',
  				'answer_author' => 'answer_author',
  				'answer_author_email' => 'answer_author_email',
	      )
	  );     
	}  
	
	protected function _init_validator()
	{
		parent :: _init_validator();

    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'question'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'answer'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/email_rule', 'question_author_email'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/email_rule', 'answer_author_email'));
	}
}

?>