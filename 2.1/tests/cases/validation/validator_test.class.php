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
require_once(LIMB_DIR . 'core/lib/util/array_dataset.class.php');
require_once(LIMB_DIR . 'core/lib/validators/validator.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/size_range_rule.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/required_rule.class.php');

Mock :: generate('rule');

class validator_test extends UnitTestCase 
{  	  
  function test_validate_no_rules()
  {
  	$v =& new validator();
  	$this->assertTrue($v->validate(new array_dataset()));
  }
      
  function test_validate_true()
  {
  	$r1 =& new Mockrule($this);
  	
  	$v =& new validator();

  	$r1->expectOnce('validate');
  	$r1->expectOnce('is_valid');
  	$r1->setReturnValue('is_valid', true);

  	$v->add_rule(&$r1);
  	
  	$v->validate(new array_dataset());
  	
  	$this->assertTrue($v->is_valid());
  	
  	$r1->tally();
  }
  
  function test_validate_false()
  {
  	$r1 =& new Mockrule($this);
  	$r2 =& new Mockrule($this);
  	
  	$v =& new validator();

  	$r1->setReturnValue('is_valid', true);
  	$r2->setReturnValue('is_valid', false);

  	$v->add_rule(&$r1);
  	$v->add_rule(&$r2);
  	
  	$v->validate(new array_dataset());
  	
  	$this->assertFalse($v->is_valid());
  }
}

?>