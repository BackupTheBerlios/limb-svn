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
require_once(LIMB_DIR . 'class/lib/util/dataspace.class.php');
require_once(LIMB_DIR . 'class/validators/rules/locale_date_rule.class.php');
require_once(LIMB_DIR . 'class/lib/db/db_table.class.php');

class locale_date_rule_test extends single_field_rule_test
{  
	function test_locale_date_rule_correct()
	{
		$this->validator->add_rule(new locale_date_rule('test', 'en'));
		
		$data =& new dataspace();
		$data->set('test', '02/28/2003');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}

	function test_locale_date_rule_error_leap_year()
	{
		$this->validator->add_rule(new locale_date_rule('test', 'en'));

		$data =& new dataspace();
		$data->set('test', '02/29/2003');

		$this->error_list->expectOnce('add_error', array('test', 'INVALID_DATE', array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}

	function test_error_locale_month_position()
	{
		$this->validator->add_rule(new locale_date_rule('test', 'en'));

		$data =& new dataspace();
		$data->set('test', '28/12/2003');

		$this->error_list->expectOnce('add_error', array('test', 'INVALID_DATE', array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}
		
	function test_locale_date_rule_error_format()
	{
		$this->validator->add_rule(new locale_date_rule('test', 'en'));

		$data =& new dataspace();
		$data->set('test', '02-29-2003');

		$this->error_list->expectOnce('add_error', array('test', 'INVALID_DATE', array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}

	function test_locale_date_rule_error()
	{
		$this->validator->add_rule(new locale_date_rule('test', 'en'));

		$data =& new dataspace();
		$data->set('test', '02jjklklak/sdsdskj34-sdsdsjkjkj78');

		$this->error_list->expectOnce('add_error', array('test', 'INVALID_DATE', array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}
} 

?>