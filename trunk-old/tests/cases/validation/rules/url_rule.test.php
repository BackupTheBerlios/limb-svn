<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: email_rule.test.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/util/dataspace.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/url_rule.class.php');

class test_url_rule extends test_single_field_rule
{
	function test_url_rule($name = 'url rule test')
	{
		parent :: test_single_field_rule($name);
	} 
	
	function test_url_rule_valid()
	{
		$this->validator->add_rule(new url_rule('test'));

		$data =& new dataspace();
		$data->set('test', 'http://wow.com.dot/this/a/valid/url');

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