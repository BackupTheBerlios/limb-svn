<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: school_news_folder_manipulation.test.php 21 2004-02-29 18:59:25Z server $
*
***********************************************************************************/ 

require_once(LIMB_DIR . '/tests/cases/site_objects/__site_object_template.test.php');

class test_poll_container_manipulation extends test_site_object_template 
{  	
  function test_poll_container_manipulation() 
  {
  	parent :: test_site_object_template();
  }

  function & _create_site_object()
  {
		$obj =& site_object_factory :: create('poll_container');
  	return $obj;
  }
  
}

?>