<?php

require_once(TEST_CASES_DIR . '/validation/rules/test_single_field_rule.php');
require_once(LIMB_DIR . '/core/lib/validators/rules/rule.class.php');
require_once(LIMB_DIR . 'core/lib/util/dataspace.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/required_rule.class.php');

class test_required_rule extends test_single_field_rule
{
	function test_required_rule()
	{
		parent::UnitTestCase();
	} 

	function test_required_rule_true()
	{
		$this->validator->add_rule(new required_rule('testfield'));

		$data = &new dataspace();
		$data->set('testfield', true);

		$this->error_list->expectNever('add_error');
		
		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	} 

	function test_required_rule_zero()
	{
		$this->validator->add_rule(new required_rule('testfield'));

		$data =& new dataspace();
		$data->set('testfield', 0);

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}
	 
	function test_required_rule_zero2()
	{
		$this->validator->add_rule(new required_rule('testfield'));

		$data =& new dataspace();
		$data->set('testfield', '0');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	} 
	
	function test_required_rule_false()
	{
		$this->validator->add_rule(new required_rule('testfield'));

		$data =& new dataspace();
		$data->set('testfield', false);

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}
	 
	function testrequired_rule_zero_length_string()
	{
		$this->validator->add_rule(new required_rule('testfield'));

		$data = &new dataspace();
		$data->set('testfield', '');

		$this->error_list->expectOnce('add_error', array('testfield', strings :: get('error_required', 'error'), array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	} 
	
	function test_required_rule_failure()
	{
		$this->validator->add_rule(new required_rule('testfield'));

		$data = &new dataspace();

		$this->error_list->expectOnce('add_error', array('testfield', strings :: get('error_required', 'error'), array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	} 
} 

?>