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

require_once(LIMB_DIR . '/tests/cases/site_objects/__site_object_template.test.php');

class test_user_groups_folder_manipulation extends test_site_object_template 
{  	
  function test_user_groups_folder_manipulation() 
  {
  	parent :: test_site_object_template();
  }

  function & _create_site_object()
  {
		$obj =& site_object_factory :: create('user_groups_folder');
  	return $obj;
  }
  
}

?>