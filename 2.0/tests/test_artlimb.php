<?php

	require_once(TEST_CASES_DIR . 'test_db_case.php');

  require_once(LIMB_DIR . 'core/tree/tree.class.php');
  require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
  require_once(LIMB_DIR . 'core/fetcher.inc.php');

  class test_limb_case extends test_db_case 
  {   	 	
  	var $dump_file = 'artlimb.sql';
  
    function test_limb_case() 
    {
    	parent :: test_db_case();
    }
    
    function _clean_up()
    {
    	parent :: _clean_up();
    	
    	purge_cache();
    	    	
    	user :: logout();
    }
       
    function _load_tree()
    {
    	$tree =& tree :: instance();
			
			if(file_exists($tree->get_cache_path()))
				unlink($tree->get_cache_path());
				
			$tree->load_tree();
    }
    
    function setUp()
    {
    	parent :: setUp();
    	
    	$this->_load_tree();
    }
            
    function _login_user($id, $groups)
    {
			$_SESSION[user :: get_session_identifier()]['id'] = $id;
			$_SESSION[user :: get_session_identifier()]['groups'] = $groups;
    }
  }
?>