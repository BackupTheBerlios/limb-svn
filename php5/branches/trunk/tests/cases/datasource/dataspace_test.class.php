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
require_once(LIMB_DIR . '/class/core/dataspace.class.php');

SimpleTestOptions::ignore('dataspace_test');

class dataspace_test extends LimbTestCase
{
	var $dataspace;
	var $filter;
	
	function setUp()
	{
		$this->dataspace = new dataspace();
	} 
	
	function tearDown()
	{
	  unset($this->dataspace);
	}
	 
	function test_get_unset_variable()
	{
		$this->assertNull($this->dataspace->get('foo'));
	} 
	
	function test_get_set_variable()
	{
		$this->dataspace->set('foo', 'bar');
		$this->assertIdentical($this->dataspace->get('foo'), 'bar');
	} 
	
	function test_get_set_array()
	{
		$array = array('red', 'blue', 'green');
		$this->dataspace->set('foo', $array);
		$this->assertIdentical($this->dataspace->get('foo'), $array);
	} 
	
	function test_get_set_object()
	{
		$foo = array('red', 'blue', 'green');
		$this->dataspace->set('foo', $foo);
		$this->assertIdentical($this->dataspace->get('foo'), $foo);
	} 
	
	function test_get_set_append()
	{
		$first = 'Hello';
		$second = 'World!';
		$this->dataspace->set('foo', $first);
		$this->dataspace->append('foo', $second);
		$this->assertIdentical($this->dataspace->get('foo'), $first . $second);
	}
  
	function test_get_reference()
	{		
		$foo =& $this->dataspace->get_reference('foo');
		$foo = 'whatever';
    $this->assertEqual($this->dataspace->get('foo'), $foo);
	}  

	function test_get_reference_default_value()
	{		
		$foo =& $this->dataspace->get_reference('foo', array());
    $this->assertIdentical($foo, array());
		$foo['test1'] = 'whatever';
    $this->assertEqual($this->dataspace->get('foo'), $foo);
	}  
	
	function test_get_set_append_mixed_type()
	{
		$first = 'Hello';
		$second = 2;
		$this->dataspace->set('foo', $first);
		$this->dataspace->append('foo', $second);
		$this->assertIdentical($this->dataspace->get('foo'), $first . $second);
	} 
	
	function test_export_empty()
	{
		$foo = array();
		$this->assertIdentical($this->dataspace->export(), $foo);
	} 
	
	function test_export()
	{
		$this->dataspace->set('foo', 'bar');
		$expected = array('foo' => 'bar');
		$this->assertIdentical($this->dataspace->export(), $expected);
	}
	 
	function test_export_import()
	{
		$numbers = array(1, 2, 3);
		$foo = array('size' => 'big', 'color' => 'red', 'numbers' => $numbers);
		$this->dataspace->import($foo);
		$exported = $this->dataspace->export();
		$this->assertIdentical($exported['size'], 'big');
		$this->assertIdentical($exported['color'], 'red');
		$this->assertIdentical($exported['numbers'], $numbers);
	} 
	
	function test_export_import_append()
	{
		$numbers = array(1, 2, 3);
		$foo = array('numbers' => $numbers);
		$bar = array('size' => 'big', 'color' => 'red');
		$this->dataspace->import($foo);
		$this->dataspace->import_append($bar);
		$exported = $this->dataspace->export();
		$this->assertIdentical($exported['size'], 'big');
		$this->assertIdentical($exported['color'], 'red');
		$this->assertIdentical($exported['numbers'], $numbers);
	} 
	function test_duplicate_import_append()
	{ 
		// experimental test case.  Should this be the proper behavior of importAppend
		// instead of what it does now?
		// I think so, why would you want to keep the original value rather than
		// using the new one? (Jon)
		$foo = array('foo' => 'kung');
		$this->dataspace->set('foo', 'bar');
		$this->dataspace->import_append($foo);
		$expected = $this->dataspace->export();
		$this->assertIdentical($expected['foo'], 'kung');
	} 
	
	function test_unset()
	{
		$array = array('rainbow' => array('color' => 'red'));
		$this->dataspace->import($array);
		
		$this->dataspace->destroy('rainbow');
		
		$this->assertNull($this->dataspace->get('rainbow'));
	}
	
  function test_merge()
  {
  	$this->dataspace->import('');
  	
  	$this->dataspace->merge('');
  	
  	$this->assertTrue(is_array($all = $this->dataspace->export()));
  	
  	$this->dataspace->import(array('people' => array('Vasa')));
  	
  	$this->dataspace->merge(array('people' => array('Vasa', 'Bob')));
  	
  	$all = $this->dataspace->export();
  	
  	$this->assertEqual(count($all['people']), 2);
  	
  	$this->assertTrue((in_array('Bob', $all['people']) && in_array('Vasa', $all['people'])));
  }
  
	function test_get_by_index_string()
	{
		$array = array('rainbow' => array('color' => 'red'));
		$this->dataspace->import($array);
		
    try
    {
      $this->dataspace->get_by_index_string('""hkljkscc');
      $this->assertTrue(false);
    }
    catch(Exception $e){}
    
    try
    {    
      $this->dataspace->get_by_index_string('["rainbow][color]');
      $this->assertTrue(false);
    }
    catch(Exception $e){}
    
    try
    {        
      $this->dataspace->get_by_index_string('[rainbow["color"]]');
      $this->assertTrue(false);
    }
    catch(Exception $e){}

		$this->assertNull($this->dataspace->get_by_index_string('[rainbow][sound]'), 'undefined index');

		$this->assertEqual($this->dataspace->get_by_index_string('[rainbow][color]'), 'red');
		$this->assertEqual($this->dataspace->get_by_index_string('[rainbow]["color"]'), 'red');
		$this->assertEqual($this->dataspace->get_by_index_string('["rainbow"][\'color\']'), 'red');
	} 
	
	function test_set_by_index_string()
	{
		$size_before = $this->dataspace->get_size();
		
		$this->dataspace->set_by_index_string('""hkljkscc', 'test');
		$this->assertEqual($this->dataspace->get_size(), $size_before, 'invalid index string, nothing should be written');
		
		$this->dataspace->set_by_index_string('["rainbow][color]', 'test');
		$this->assertEqual($this->dataspace->get_size(), $size_before, 'wrong quotation nesting, nothing should be written');

		$this->dataspace->set_by_index_string('[rainbow["color"]]', 'test');
		$this->assertEqual($this->dataspace->get_size(), $size_before, 'wrong brackets nesting, nothing should be written');
		
		$this->dataspace->set_by_index_string('[rainbow][color]', array(1 => 'red'));
		$this->assertEqual($this->dataspace->vars['rainbow']['color'], array(1 => 'red'));

		$this->dataspace->set_by_index_string('[rainbow]["color"]', '"red"');
		$this->assertEqual($this->dataspace->vars['rainbow']['color'], '"red"');

		$this->dataspace->set_by_index_string('["rainbow"][\'color\']', 10);
		$this->assertEqual($this->dataspace->vars['rainbow']['color'], 10);
	} 
} 
?>