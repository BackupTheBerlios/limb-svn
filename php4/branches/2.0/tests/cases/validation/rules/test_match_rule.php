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
require_once(LIMB_DIR . 'core/lib/validators/rules/match_rule.class.php');

class test_match_rule extends test_single_field_rule
{
	function test_match_rule($name = 'match rule test case')
	{
		parent :: UnitTestCase($name);
	}
	 
	function test_match_rule_true()
	{
		$this->validator->add_rule(new match_rule('testfield', 'testmatch'));

		$data =& new dataspace();
		$data->set('testfield', 'peaches');
		$data->set('testmatch', 'peaches');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	} 
	
	function test_match_rule_empty()
	{
		$this->validator->add_rule(new match_rule('testfield', 'testmatch'));

		$data = &new dataspace();

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}
	 
	function test_match_rule_empty2()
	{
		$this->validator->add_rule(new match_rule('testfield', 'testmatch'));

		$data = &new dataspace();
		$data->set('testfield', 'peaches');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}
	 
	function test_match_rule_empty3()
	{
		$this->validator->add_rule(new match_rule('testfield', 'testmatch'));

		$data = &new dataspace();
		$data->set('testmatch', 'peaches');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}
	 
	function test_match_rule_failure()
	{
		$this->validator->add_rule(new match_rule('testfield', 'testmatch'));

		$data =& new dataspace();
		$data->set('testfield', 'peaches');
		$data->set('testmatch', 'cream');

		$this->error_list->expectOnce('add_error', array('testfield', 'NO_MATCH', array('match_field' => 'testmatch')));
		
		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	} 
} 

?>