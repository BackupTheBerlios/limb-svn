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
require_once(LIMB_DIR . '/core/lib/validators/rules/rule.class.php');
require_once(LIMB_DIR . 'core/lib/util/dataspace.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/required_rule.class.php');

class required_rule_test extends single_field_rule_test
{
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