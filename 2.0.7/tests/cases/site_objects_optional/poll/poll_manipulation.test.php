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

class test_poll_manipulation extends test_content_object_template 
{  	
  function test_poll_manipulation() 
  {
  	parent :: test_content_object_template();
  }

  function & _create_site_object()
  {
		$obj =& site_object_factory :: create('poll');
  	return $obj;
  }
  
  function _set_object_initial_attributes()
  {
  	$this->object->set_attribute('restriction', 1);
  	$this->object->set_attribute('start_date', '2003-02-21');
  	$this->object->set_attribute('finish_date', '2003-02-27');
  }
	
	function _set_object_secondary_update_attributes()
	{
  	$this->object->set_attribute('restriction', 2);
  	$this->object->set_attribute('start_date', '2003-03-21');
  	$this->object->set_attribute('finish_date', '2003-03-27');
	}
  
}

?>