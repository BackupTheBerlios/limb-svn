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

require_once(LIMB_DIR . 'core//template/component.class.php');
require_once(LIMB_DIR . 'core//template/tag_component.class.php');

if (! class_exists('data_space_test_case'))
	require_once(TEST_CASES_DIR . '/test_dataspace.php');

Mock::generate('component', 'mock_component');

class component_test_case extends data_space_test_case
{
	function component_test_case($name = 'component_test_case')
	{
		$this->UnitTestCase($name);
	} 
	function setUp()
	{
		$this->dataspace =& new component();
	} 
	function tearDown()
	{
		unset ($this->dataspace);
	} 
	function test_get_rerver_id()
	{
		$this->dataspace->id = 'TestId';
		$this->assertEqual($this->dataspace->get_server_id(), 'TestId');
	} 
	function test_find_child()
	{
		$child = &new mock_component($this);
		$child->id = 'TestChild';
		$this->dataspace->add_child($child, 'TestChild');
		$this->assertIsA($this->dataspace->find_child('TestChild'), 'mock_component');
	} 
	function test_find_child_not_found()
	{
		$this->assertFalse($this->dataspace->find_child('TestChild'));
	} 
	function test_find_child_by_class()
	{
		$child = &new mock_component($this);
		$this->dataspace->add_child($child, 'TestChild');
		$this->assertIsA($this->dataspace->find_child_by_class('mock_component'), 'mock_component');
	} 
	function test_find_child_by_class_not_found()
	{
		$this->assertFalse($this->dataspace->find_child_by_class('test_component'));
	} 
	function test_find_parent_by_chilld()
	{
		$component = &new component();
		$component->id = 'TestParent';
		$component->add_child($this->dataspace, 'test_component');
		$this->assertIsA($this->dataspace->find_parent_by_class('component'), 'component');
	} 
	function test_find_parent_by_class_not_found()
	{
		$this->assertFalse($this->dataspace->find_parent_by_class('test_component'));
	} 
} 

class tag_component_test_case extends component_test_case
{
	function tag_component_test_case($name = 'tag_component_test_case')
	{
		$this->UnitTestCase($name);
	} 
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