<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'class/core/actions/action_factory.class.php');
require_once(LIMB_DIR . 'class/core/actions/action.class.php');

class action_for_action_factory_test extends action{}

class action_factory_test extends LimbTestCase 
{  	  
  function setUp()
  {
  	debug_mock :: init($this);
  }
  
  function tearDown()
  {
  	debug_mock :: tally();
  }
  
  function test_create_ok()
  {
  	$c =& action_factory :: create('action_for_action_factory_test');
  	
  	$this->assertIsA($c, 'action_for_action_factory_test');	
  } 
  
  function test_create_no_such_action()
  {
  	//debug_mock :: expect_write_error('action not found', array('class_path' => 'no_such_action'));
  	debug_mock :: expect_write_exception(new FileNotFoundException('action not found', 'no_such_action'));
  	
  	$c =& action_factory :: create('no_such_action');
  	
  	$this->assertIsA($c, 'empty_action'); 
  }
}

?>