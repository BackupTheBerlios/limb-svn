<?php
  
  require_once(LIMB_DIR . 'core/actions/action.class.php');
 
  class test_action extends UnitTestCase 
  {
  	var $a = null;
  	
    function test_action() 
    {
    	parent :: UnitTestCase();
    }
    
    function setUp()
    {    	
    	$this->a =& new action();
    }
    
    function tearDown()
    {
    }
    
    function test_init()
    {
    	$this->assertNotNull($this->a);
    }    
  }
  
?>