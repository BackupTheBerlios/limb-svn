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
require_once(LIMB_DIR . '/core/lib/system/sys.class.php');
require_once(LIMB_DIR . '/core/lib/system/dir.class.php');

define('ABSOLUTE_PATH', PROJECT_DIR . '/var/');
define('RELATIVE_PATH', 'var');

class test_dir extends UnitTestCase 
{
  function test_dir() 
  {
  	parent :: UnitTestCase();
  }
  
  function setUp()
  {
  	$this->_clean_up();
  }
  
  function tearDown()
  {
  	$this->_clean_up();
  }
  
  function _clean_up()
  {
  	if(is_dir(ABSOLUTE_PATH . '/tmp/wow/hey'))
  		rmdir(ABSOLUTE_PATH . '/tmp/wow/hey');	

  	if(is_dir(ABSOLUTE_PATH . '/tmp/wow'))
  		rmdir(ABSOLUTE_PATH . '/tmp/wow');	

  	if(is_dir(ABSOLUTE_PATH . '/tmp'))
  		rmdir(ABSOLUTE_PATH . '/tmp');
  		
  	if(is_dir(RELATIVE_PATH . '/tmp/wow/hey'))
  		rmdir(RELATIVE_PATH . '/tmp/wow/hey');	

  	if(is_dir(RELATIVE_PATH . '/tmp/wow'))
  		rmdir(RELATIVE_PATH . '/tmp/wow');	

  	if(is_dir(RELATIVE_PATH . '/tmp'))
  		rmdir(RELATIVE_PATH . '/tmp');

  }
        
  function test_mkdir_absolute_path() 
  {  	
  	dir :: mkdir(ABSOLUTE_PATH . '/./tmp\../tmp/wow////hey/', 0777);
  	
  	$this->assertTrue(is_dir(ABSOLUTE_PATH . '/tmp/wow/hey/'));
  }    
  
  function test_mkdir_relative_path() 
  { 
  	dir :: mkdir(RELATIVE_PATH . '/./tmp\../tmp/wow////hey/', 0777);
  	
  	$this->assertTrue(is_dir(RELATIVE_PATH . '/tmp/wow/hey/'));
  }    

}

?>