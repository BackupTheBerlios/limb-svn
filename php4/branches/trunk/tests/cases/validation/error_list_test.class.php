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
require_once(LIMB_DIR . 'class/validators/error_list.class.php');
	
class error_list_test extends LimbTestCase 
{  	
  function error_list_test() 
  {
  	parent :: LimbTestCase();
  }
  
  function setUp()
  {
  	$e =& error_list :: instance();
  	$e->reset();
  }
  
  function tearDown()
  {
  	$e =& error_list :: instance();
  	$e->reset();
  }
      
  function test_instance()
  {
  	$e =& error_list :: instance();
  	    	
  	$this->assertNotNull($e);
  	$this->assertIsA($e, 'error_list');
  	
  	$e2 =& error_list :: instance();
  	
  	$this->assertReference(&$e, &$e2);
  }
  
  function test_add_error()
  {
  	$e =& error_list :: instance();
  	
  	$e->add_error('test', 'error');
  	
  	$errors = $e->get_errors('test');
  	
  	$this->assertEqual(sizeof($errors), 1);
  	$this->assertEqual($errors[0]['error'], 'error');
  	
  	$e->add_error('test', 'error2', array('param' => 1));
		
		$errors = $e->get_errors('test');
		
  	$this->assertEqual(sizeof($errors), 2);
  	$this->assertEqual($errors[1]['error'], 'error2');
  	$this->assertEqual($errors[1]['params']['param'], 1);
  	
  	$errors = $e->get_errors('no_errors');
  	$this->assertNull($errors);
  } 
}

?>