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
require_once(LIMB_DIR . 'core/lib/validators/rules/single_field_rule.class.php');

class test_single_field_rule extends test_rule 
{  	
  function test_single_field_rule() 
  {
  	parent :: test_rule();
  }
          
  function test_init()
  {
  	$r = new single_field_rule('test');    	
  	$this->assertEqual($r->get_field_name(), 'test');
  }   
 
}

?>