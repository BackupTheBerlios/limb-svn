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
require_once(LIMB_DIR . 'core/tree/limb_tree.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'core/fetcher.class.php');

require_once(LIMB_DIR . 'tests/cases/test_db_case.php');

SimpleTestOptions::ignore('test_limb_case');

class test_limb_case extends test_db_case 
{   	 		
  function test_limb_case() 
  {
  	parent :: test_db_case();
  }
      
  function tearDown()
  {
  	parent :: tearDown();
  	
  	$user =& user :: instance();
  	$user->logout();
  }
      
  function _login_user($id, $groups)
  {
  	$user =& user :: instance();
  	
  	$user->_set_id($id);
  	$user->_set_groups($groups);  	
  }
}
?>