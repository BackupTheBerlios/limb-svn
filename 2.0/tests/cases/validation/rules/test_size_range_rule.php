<?php

require_once(TEST_CASES_DIR . '/validation/rules/test_single_field_rule.php');
require_once(LIMB_DIR . 'core/lib/util/dataspace.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/size_range_rule.class.php');

class test_size_range_rule extends test_single_field_rule
{
	function size_range_rule_test_case($name = 'size range rule test case')
	{
		parent :: UnitTestCase($name);
	} 
	
	function test_size_range_rule_empty()
	{
		$this->validator->add_rule(new size_range_rule('testfield', 10));

		$data =& new dataspace();

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	} 
	
	function test_size_range_rule_blank()
	{
		$this->validator->add_rule(new size_range_rule('testfield', 5, 10));

		$data =& new dataspace();
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

		$this->error_list->expectOnce('add_error', array('testfield', 'SIZE_TOO_SMALL', array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	} 
	
	function testsize_range_ruleTooBig()
	{
		$this->validator->add_rule(new size_range_rule('testfield', 10));

		$data = &new dataspace();
		$data->set('testfield', '12345678901234567890');

		$this->error_list->expectOnce('add_error', array('testfield', 'SIZE_TOO_BIG', array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	} 
	
	function testsize_range_ruleTooBig2()
	{
		$this->validator->add_rule(new size_range_rule('testfield', 5, 10));

		$data = &new dataspace();
		$data->set('testfield', '12345678901234567890');

		$this->error_list->expectOnce('add_error', array('testfield', 'SIZE_TOO_BIG', array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	} 
	
	function testsize_range_ruleTooSmall()
	{
		$this->validator->add_rule(new size_range_rule('testfield', 30, 100));

		$data = &new dataspace();
		$data->set('testfield', '12345678901234567890');

		$this->error_list->expectOnce('add_error', array('testfield', 'SIZE_TOO_SMALL', array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	} 
} 

?>