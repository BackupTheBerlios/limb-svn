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
    
  function test_create_ok()
  {
  	$c =& action_factory :: create('action');
  	
  	$this->assertIsA($c, 'action');	
  } 
  
  function test_create_no_such_action()
  {
  	$c =& action_factory :: create('no_such_action');
  	
  	$this->assertErrorPattern('/action not found/');
  	
  	$this->assertIsA($c, 'empty_action'); 
  }
}

?>