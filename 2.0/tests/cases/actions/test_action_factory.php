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
require_once(LIMB_DIR . 'core/actions/action_factory.class.php');

class test_action_factory extends UnitTestCase 
{  	
  function test_action_factory() 
  {
  	parent :: UnitTestCase();
  }
  
  function setUp()
  {
  	debug_mock :: init($this);
  }
  
  function tearDown()
  {
  	debug_mock :: tally();
  }
  
  function test_create()
  {
  	$c =& action_factory :: create('action');
  	
  	$this->assertNotNull($c);
  	
  	debug_mock :: expect_write_error('action not found', array('class_path' => 'no_such_action'));
  	
  	$c =& action_factory :: create('no_such_action');
  	
  	$this->assertNull($c);    	
  }    
}

?>