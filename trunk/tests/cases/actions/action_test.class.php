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
require_once(LIMB_DIR . 'class/actions/action.class.php');

class action_test extends LimbTestCase 
{
	var $a = null;
  	  
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