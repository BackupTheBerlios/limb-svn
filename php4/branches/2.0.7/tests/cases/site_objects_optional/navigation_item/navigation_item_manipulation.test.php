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

class test_navigation_item_manipulation extends test_content_object_template 
{  	
  function test_navigation_item_manipulation() 
  {
  	parent :: test_content_object_template();
  }

  function & _create_site_object()
  {
		$obj =& site_object_factory :: create('navigation_item');
  	return $obj;
  }
  
  function _set_object_initial_attributes()
  {
  	$this->object->set_attribute('url', 'http://www.site.com');
  }
	
	function _set_object_secondary_update_attributes()
	{
  	$this->object->set_attribute('url', 'http://www.site2.com');
	}

  
}

?>