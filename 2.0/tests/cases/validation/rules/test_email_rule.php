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


require_once(TEST_CASES_DIR . '/validation/rules/test_single_field_rule.php');
require_once(LIMB_DIR . 'core/lib/util/dataspace.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/email_rule.class.php');

class test_email_rule extends test_single_field_rule
{
	function test_email_rule($name = 'test_email_rule')
	{
		parent :: UnitTestCase($name);
	} 
	
	function test_email_rule_valid()
	{
		$this->validator->add_rule(new email_rule('test'));

		$data =& new dataspace();
		$data->set('test', 'billgates@microsoft.com');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	} 
	
	function test_email_rule_invalid()
	{
		$this->validator->add_rule(new email_rule('testfield'));

		$data =& new dataspace();
		$data->set('testfield', 'billgatesmicrosoft.com');

		$this->error_list->expectOnce('add_error', array('testfield', 'EMAIL_INVALID', array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	} 
	
	function test_email_rule_invalid_user()
	{
		$this->validator->add_rule(new email_rule('testfield'));

		$Data = &new dataspace();
		$Data->set('testfield', 'bill(y!)gates@microsoft.com');

		$this->error_list->expectOnce('add_error', array('testfield', 'EMAIL_INVALID_USER', array()));

		$this->validator->validate($Data);
		$this->assertFalse($this->validator->is_valid());
	} 
	
	function test_email_user_invalid_domain()
	{
		$this->validator->add_rule(new email_rule('testfield'));

		$Data = &new dataspace();
		$Data->set('testfield', 'billgates@micro$oft.com');

		$this->error_list->expectOnce('add_error', array('testfield', 'BAD_DOMAIN_CHARACTERS', array()));

		$this->validator->validate($Data);
		$this->assertFalse($this->validator->is_valid());
	}
} 

?>