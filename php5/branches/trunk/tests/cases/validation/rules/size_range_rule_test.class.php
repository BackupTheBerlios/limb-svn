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
require_once(dirname(__FILE__) . '/single_field_rule_test.class.php');
require_once(LIMB_DIR . '/class/core/dataspace.class.php');
require_once(LIMB_DIR . '/class/validators/rules/size_range_rule.class.php');

class size_range_rule_test extends single_field_rule_test
{	
	function test_size_range_rule_empty()
	{
		$this->validator->add_rule(new size_range_rule('testfield', 10));

		$data = new dataspace();

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	} 
	
	function test_size_range_rule_blank()
	{
		$this->validator->add_rule(new size_range_rule('testfield', 5, 10));

		$data = new dataspace();
		$data->set('testfield', '');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	} 
	
	function testsize_range_rule_zero()
	{
		$this->validator->add_rule(new size_range_rule('testfield', 5, 10));

		$data = &new dataspace();
		$data->set('testfield', '0');

		$this->error_list->expectOnce('add_error', array('testfield', strings :: get('size_too_small', 'error'), array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	} 
	
	function testsize_range_ruleTooBig()
	{
		$this->validator->add_rule(new size_range_rule('testfield', 10));

		$data = &new dataspace();
		$data->set('testfield', '12345678901234567890');

		$this->error_list->expectOnce('add_error', array('testfield', strings :: get('size_too_big', 'error'), array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	} 
	
	function testsize_range_ruleTooBig2()
	{
		$this->validator->add_rule(new size_range_rule('testfield', 5, 10));

		$data = &new dataspace();
		$data->set('testfield', '12345678901234567890');

		$this->error_list->expectOnce('add_error', array('testfield', strings :: get('size_too_big', 'error'), array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	} 
	
	function testsize_range_ruleTooSmall()
	{
		$this->validator->add_rule(new size_range_rule('testfield', 30, 100));

		$data = &new dataspace();
		$data->set('testfield', '12345678901234567890');

		$this->error_list->expectOnce('add_error', array('testfield', strings :: get('size_too_small', 'error'), array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	} 
} 

?>