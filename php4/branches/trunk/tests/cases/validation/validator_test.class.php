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
require_once(LIMB_DIR . 'class/core/array_dataset.class.php');
require_once(LIMB_DIR . 'class/validators/validator.class.php');
require_once(LIMB_DIR . 'class/validators/rules/size_range_rule.class.php');
require_once(LIMB_DIR . 'class/validators/rules/required_rule.class.php');

Mock::generate('error_list');

Mock::generatePartial(
    'validator',
    'validator_test_version2',
    array('_get_error_list')); 

Mock :: generate('rule');

class validator_test extends LimbTestCase 
{
  var $error_list = null;
  var $validator = null;
  
	function setUp()
	{
   $this->error_list =& new Mockerror_list($this);
   $this->validator =& new validator_test_version2($this);
   $this->validator->setReturnReference('_get_error_list', $this->error_list);
	} 
	  	  
  function test_validate_no_rules()
  {
  	$this->assertTrue($this->validator->validate(new array_dataset()));
  }
      
  function test_validate_true()
  {
  	$r1 =& new Mockrule($this);
  	
  	$r1->expectOnce('validate');
  	$r1->expectOnce('is_valid');
  	$r1->setReturnValue('is_valid', true);

  	$this->validator->add_rule(&$r1);
  	
  	$this->validator->validate(new array_dataset());
  	
  	$this->assertTrue($this->validator->is_valid());
  	
  	$r1->tally();
  }
  
  function test_validate_false()
  {
  	$r1 =& new Mockrule($this);
  	$r2 =& new Mockrule($this);
  	
  	$r1->setReturnValue('is_valid', true);
  	$r2->setReturnValue('is_valid', false);

  	$this->validator->add_rule(&$r1);
  	$this->validator->add_rule(&$r2);
  	
  	$this->validator->validate(new array_dataset());
  	
  	$this->assertFalse($this->validator->is_valid());
  }
  
  function test_add_error()
  {
    $this->validator->add_error('test', 'error', array('1' => 'error'));
    $this->error_list->expectOnce('add_error', array('test', 'error', array('1' => 'error')));
  }
}

?>