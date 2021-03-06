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

class test_message_manipulation extends test_content_object_template 
{  	
  function test_message_manipulation() 
  {
  	parent :: test_content_object_template();
  }

  function & _create_site_object()
  {
		$obj =& site_object_factory :: create('message');
  	return $obj;
  }
  
  function _set_object_initial_attributes()
  {
  	$this->object->set_attribute('content', 'some content');
  }
	
	function _set_object_secondary_update_attributes()
	{
  	$this->object->set_attribute('content', 'some content');
	}

  
}

?>