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

require_once(LIMB_DIR . '/tests/cases/site_objects/_content_object_template.test.php');

class test_faq_object_manipulation extends test_content_object_template 
{  	
  function test_faq_object_manipulation() 
  {
  	parent :: test_content_object_template();
  }

  function & _create_site_object()
  {
		$obj =& site_object_factory :: create('faq_object');
  	return $obj;
  }
  
  function _set_object_initial_attributes()
  {
  	$this->object->set_attribute('question', 'Question');
  	$this->object->set_attribute('question_author', 'Question author');
  	$this->object->set_attribute('question_author_email', 'Question author email');
  	$this->object->set_attribute('answer', 'Answer');
  	$this->object->set_attribute('answer_author', 'Answer author');
  	$this->object->set_attribute('answer_author_email', 'Answer author email');
  }
	
	function _set_object_secondary_update_attributes()
	{
  	$this->object->set_attribute('question', 'Question 2');
  	$this->object->set_attribute('question_author', 'Question author 2');
  	$this->object->set_attribute('question_author_email', 'Question author email 2');
  	$this->object->set_attribute('answer', 'Answer 2');
  	$this->object->set_attribute('answer_author', 'Answer author 2');
  	$this->object->set_attribute('answer_author_email', 'Answer author email 2');
	}
}
?>