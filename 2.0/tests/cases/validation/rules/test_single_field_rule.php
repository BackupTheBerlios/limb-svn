<?php

require_once('test_rule.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/single_field_rule.class.php');
	
class test_single_field_rule extends test_rule 
{  	
  function test_single_field_rule() 
  {
  	parent :: UnitTestCase();
  }
          
  function test_init()
  {
  	$r = new single_field_rule('test');    	
  	$this->assertEqual($r->get_field_name(), 'test');
  }   
 
}

?>