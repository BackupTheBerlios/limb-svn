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
require_once(LIMB_DIR . 'class/template/component.class.php');
require_once(LIMB_DIR . '/tests/cases/dataspace_test.class.php');

Mock::generate('component', 'mock_component');

class component_test extends dataspace_test
{	 
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
		$this->assertFalse($this->dataspace->find_child_by_class('component_test'));
	} 
	
	function test_find_parent_by_chilld()
	{
		$component = &new component();
		$component->id = 'TestParent';
		$component->add_child($this->dataspace, 'component_test');
		$this->assertIsA($this->dataspace->find_parent_by_class('component'), 'component');
	} 
	
	function test_find_parent_by_class_not_found()
	{
		$this->assertFalse($this->dataspace->find_parent_by_class('component_test'));
	} 
} 
?>