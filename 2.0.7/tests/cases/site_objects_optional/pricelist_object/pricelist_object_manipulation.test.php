<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: school_news_object_manipulation.test.php 21 2004-02-29 18:59:25Z server $
*
***********************************************************************************/ 

require_once(LIMB_DIR . '/tests/cases/site_objects/_content_object_template.test.php');

class test_pricelist_object_manipulation extends test_content_object_template 
{  	
  function test_pricelist_object_manipulation() 
  {
  	parent :: test_content_object_template();
  }

  function & _create_site_object()
  {
		$obj =& site_object_factory :: create('pricelist_object');
  	return $obj;
  }
  
  function _set_object_initial_attributes()
  {
  	$this->object->set_attribute('content', 'pricelist object content');  	
  	$this->object->set_attribute('file_id', '123');
  }
	
	function _set_object_secondary_update_attributes()
	{
  	$this->object->set_attribute('content', 'pricelist object content');  	
  	$this->object->set_attribute('file_id', '123');
	}
}
?>