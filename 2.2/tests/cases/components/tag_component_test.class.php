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
require_once(LIMB_DIR . 'core/lib/util/dataspace.class.php');
require_once(LIMB_DIR . 'core/template/tag_component.class.php');

if (! class_exists('dataspace_test_case'))
	require_once(LIMB_DIR . '/tests/cases/dataspace_test.class.php');

class tag_component_test extends component_test
{
	function setUp()
	{
		$this->dataspace =& new tag_component();
	} 
	
	function test_get_client_id()
	{
		$this->dataspace->set_attribute('id', 'TestId');
		$this->assertEqual($this->dataspace->get_client_id(), 'TestId');
	} 
	
	function test_get_client_id_unset()
	{
		$this->assertNull($this->dataspace->get_client_id());
	} 
	
	function test_get_attribute()
	{
		$this->dataspace->set_attribute('class', 'Test');
		$this->assertEqual($this->dataspace->get_attribute('class'), 'Test');
	} 
	
	function test_get_unset_attribute()
	{
		$this->assertNull($this->dataspace->get_attribute('class'));
	} 
	
	function test_has_attribute()
	{
		$this->dataspace->set_attribute('class', 'Test');
		$this->assertTrue($this->dataspace->has_attribute('class'));
	} 
	
	function test_has_attribute_unset()
	{
		$this->assertFalse($this->dataspace->has_attribute('class'));
	} 
	
	function test_render_attributes()
	{
		$this->dataspace->set_attribute('a', 'red');
		$this->dataspace->set_attribute('b', 'blue');
		$this->dataspace->set_attribute('c', 'green');
		ob_start();
		$this->dataspace->render_attributes();
		$output = ob_get_contents();
		ob_end_clean();
		$this->assertEqual(' a="red" b="blue" c="green"', $output);
	} 
} 

?>