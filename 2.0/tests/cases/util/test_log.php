<?php
	
  require_once(LIMB_DIR . '/core/lib/util/log.class.php');

  class test_log extends UnitTestCase 
  {
    function test_log() 
    {
    	parent :: UnitTestCase();
    }
    
    function test_writing_to_file() 
    {      
      log :: write(array('tmp/', 'test.log'), 'wow');
      
      $this->assertTrue(file_exists('tmp/test.log'));
      
      $arr = file('tmp/test.log');
      
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
    	if(file_exists('tmp/test.log'))
      	unlink('tmp/test.log');    	
    }
    
    function tearDown()
    {
    	if(file_exists('tmp/test.log'))
      	unlink('tmp/test.log');    	
    }
  }
  
?>