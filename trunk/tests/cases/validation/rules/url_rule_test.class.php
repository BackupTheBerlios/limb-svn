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
require_once(LIMB_DIR . 'class/core/dataspace.class.php');
require_once(LIMB_DIR . 'class/validators/rules/url_rule.class.php');

class url_rule_test extends single_field_rule_test
{
	function test_url_rule_valid()
	{
		$this->validator->add_rule(new url_rule('test'));

		$data =& new dataspace();
		$data->set('test', 'https://wow.com.dot:81/this/a/valid/url?hey=wow&test');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	} 
	
	function test_url_rule_invalid()
	{
		$this->validator->add_rule(new url_rule('testfield'));

		$data =& new dataspace();
		$data->set('testfield', '://not/a/valid/url');

		$this->error_list->expectOnce('add_error', array('testfield', 'BAD_URL', array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}
} 

?>