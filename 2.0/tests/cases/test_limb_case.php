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
require_once(TEST_CASES_DIR . 'test_db_case.php');

require_once(LIMB_DIR . 'core/tree/limb_tree.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'core/fetcher.class.php');

class test_limb_case extends test_db_case 
{   	 		
  function test_limb_case() 
  {
  	parent :: test_db_case();
  }
      
  function tearDown()
  {
  	parent :: tearDown();
  	
  	user :: logout();
  }
      
  function _login_user($id, $groups)
  {
		$_SESSION[user :: get_session_identifier()]['id'] = $id;
		$_SESSION[user :: get_session_identifier()]['groups'] = $groups;
  }
}
?>