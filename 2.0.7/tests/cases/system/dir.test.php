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

class test_dir extends UnitTestCase 
{
  function test_dir() 
  {
  	parent :: UnitTestCase();
  }
  
  function setUp()
  {
  	if(!is_dir(VAR_DIR . '/tmp'))
  		mkdir(VAR_DIR . '/tmp');	
  }
  
  function tearDown()
  {
  	if(is_dir(VAR_DIR . '/tmp/wow/hey'))
  		rmdir(VAR_DIR . '/tmp/wow/hey');	

  	if(is_dir(VAR_DIR . '/tmp/wow'))
  		rmdir(VAR_DIR . '/tmp/wow');	

  	if(is_dir(VAR_DIR . '/tmp'))
  		rmdir(VAR_DIR . '/tmp');	
  }
      
  function test_mkdir_windows() 
  {
  	if(sys :: os_type() != 'win32')
  		return;
  	
  	dir :: mkdir(VAR_DIR . '/./tmp\../tmp/wow////hey/', 0777, true);
  	
  	$this->assertTrue(is_dir(VAR_DIR . '/tmp/wow/hey/'));
  }    
}

?>