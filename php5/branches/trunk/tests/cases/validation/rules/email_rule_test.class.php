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
require_once(LIMB_DIR . '/class/validators/rules/email_rule.class.php');

class email_rule_test extends single_field_rule_test
{	
	function test_email_rule_valid()
	{
		$this->validator->add_rule(new email_rule('test'));

		$data = new dataspace();
		$data->set('test', 'billgates@microsoft.com');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	} 

	function test_email_rule_valid2()
	{
		$this->validator->add_rule(new email_rule('test'));

		$data = new dataspace();
		$data->set('test', 'billgates_@microsoft.com');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	} 

	function test_email_rule_valid3()
	{
		$this->validator->add_rule(new email_rule('test'));

		$data = new dataspace();
		$data->set('test', 'bill_gates_@microsoft.com');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	} 

	function test_email_rule_valid4()
	{
		$this->validator->add_rule(new email_rule('test'));

		$data = new dataspace();
		$data->set('test', 'bill-gates@microsoft.com');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	} 
	
	function test_email_rule_invalid_user()
	{
		$this->validator->add_rule(new email_rule('testfield'));

		$data = new dataspace();
		$data->set('testfield', 'bill(y!)gates@microsoft.com');

		$this->error_list->expectOnce('add_error', array('testfield', strings :: get('invalid_email', 'error'), array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	} 

	function test_email_rule_invalid_user2()
	{
		$this->validator->add_rule(new email_rule('testfield'));

		$data = new dataspace();
		$data->set('testfield', '_bill.gates@microsoft.com');

		$this->error_list->expectOnce('add_error', array('testfield', strings :: get('invalid_email', 'error'), array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	} 
	
	function test_email_user_invalid_domain()
	{
		$this->validator->add_rule(new email_rule('testfield'));

		$data = new dataspace();
		$data->set('testfield', 'billgates@micro$oft.com');

		$this->error_list->expectOnce('add_error', array('testfield', strings :: get('bad_domain_characters', 'error'), array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}
} 

?>