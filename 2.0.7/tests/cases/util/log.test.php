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
require_once(LIMB_DIR . '/core/lib/util/log.class.php');

class test_log extends UnitTestCase 
{
  function test_log() 
  {
  	parent :: UnitTestCase();
  }
  
  function test_writing_to_file() 
  {      
    log :: write(array(VAR_DIR . '/tmp/', 'test.log'), 'wow');
    
    $this->assertTrue(file_exists(VAR_DIR . '/tmp/test.log'));
    
    $arr = file(VAR_DIR . '/tmp/test.log');
    
    $this->assertNotNull($arr[0]);
    
    if(isset($_SERVER['REQUEST_URI']))
	    $this->assertWantedPattern(
	    	'|' . preg_quote($_SERVER['REQUEST_URI']) . '|', 
	    	$arr[1]);
    	
    $this->assertWantedPattern(
    	'|wow|', 
    	$arr[2]);

  }
  
  function setUp()
  {
  	if(file_exists(VAR_DIR . '/tmp/test.log'))
    	unlink(VAR_DIR . '/tmp/test.log');    	
  }
  
  function tearDown()
  {
  	if(file_exists(VAR_DIR . '/tmp/test.log'))
    	unlink(VAR_DIR . '/tmp/test.log');    	
  }
}

?>