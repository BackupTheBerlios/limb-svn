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
require_once(LIMB_DIR . 'class/core/dataspace.class.php');
require_once(LIMB_DIR . 'class/validators/rules/us_zip_rule.class.php');

class us_zip_rule_test extends single_field_rule_test
{
	function test_us_zip_rule_valid()
	{
		$this->validator->add_rule(new us_zip_rule('test'));

		$data =& new dataspace();
		$data->set('test', '49007');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	} 

	function test_us_zip_rule_valid2()
	{
		$this->validator->add_rule(new us_zip_rule('test'));

		$data =& new dataspace();
		$data->set('test', '49007 1234');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	} 
	
	function test_us_zip_rule_invalid1()
	{
		$this->validator->add_rule(new us_zip_rule('test'));

		$data =& new dataspace();
		$data->set('test', '490078');

		$this->error_list->expectOnce('add_error', array('test', strings :: get('error_invalid_zip_format', 'error'), array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}
	
	function test_us_zip_rule_invalid2()
	{
		$this->validator->add_rule(new us_zip_rule('test'));

		$data =& new dataspace();
		$data->set('test', '49007 23234');

		$this->error_list->expectOnce('add_error', array('test', strings :: get('error_invalid_zip_format', 'error'), array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}
	
	function test_us_zip_rule_invalid3()
	{
		$this->validator->add_rule(new us_zip_rule('test'));

		$data =& new dataspace();
		$data->set('test', '4t007 12d4');

		$this->error_list->expectOnce('add_error', array('test', strings :: get('error_invalid_zip_format', 'error'), array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}	
	
} 

?>