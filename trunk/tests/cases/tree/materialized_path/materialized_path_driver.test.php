<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: nested_sets_driver.test.php 131 2004-04-09 14:11:45Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/tree/drivers/materialized_path_driver.class.php');

define('MATERIALIZED_PATH_TEST_TABLE', 'test_materialized_path_tree');

class materialized_path_driver_test_version extends materialized_path_driver
{	
	function materialized_path_driver_test_version()
	{
		$this->_node_table = MATERIALIZED_PATH_TEST_TABLE;
		
		parent :: materialized_path_driver();
	}
}

class test_materialized_path_driver extends UnitTestCase
{
	var $db = null;
	var $driver = null;
	
  function test_materialized_path_driver() 
  {
  	parent :: UnitTestCase();
  	
 		$this->db = db_factory :: instance();
  }

	function setUp()
	{
		debug_mock :: init($this);
		
		$this->driver = new materialized_path_driver_test_version();
		
		$this->_clean_up();
	} 

	function tearDown()
	{
		debug_mock :: tally();
		
		$this->_clean_up();
	} 
	
	function _clean_up()
	{
		$this->db->sql_delete(MATERIALIZED_PATH_TEST_TABLE);
	}
	
	function test_get_node()
	{
		$node = array(
			'identifier' => 'test', 
			'object_id' => 100, 
			'id' => 10, 
			'path' => '/10/', 
			'root_id' => 10,
			'ordr' => 100,
			'level' => 2,
			'parent_id' => 1000
		);

		$this->db->sql_insert(MATERIALIZED_PATH_TEST_TABLE, $node);
		
		$this->assertEqual($node, $this->driver->get_node(10));
		
		$this->assertIdentical(false, $this->driver->get_node(10000));
	}
	
	function test_get_parent()
	{
		$root_node = array(
			'identifier' => 'root', 
			'object_id' => 100, 
			'id' => 1, 
			'path' => '/1/', 
			'root_id' => 1,
			'ordr' => 1,
			'level' => 1,
			'parent_id' => 0
		);

		$this->db->sql_insert(MATERIALIZED_PATH_TEST_TABLE, $root_node);

		$node = array(
			'identifier' => 'test', 
			'object_id' => 100, 
			'id' => 10, 
			'path' => '/1/10/', 
			'root_id' => 1,
			'ordr' => 1,
			'level' => 2,
			'parent_id' => 1
		);

		$this->db->sql_insert(MATERIALIZED_PATH_TEST_TABLE, $node);
		
		$this->assertEqual($root_node, $this->driver->get_parent(10));
		$this->assertIdentical(false, $this->driver->get_parent(1));
		
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('id' => 1000));
		$this->assertIdentical(false, $this->driver->get_parent(1000));
	}
		
	function test_create_root_node()
	{
		$node = array(
			'identifier' => 'test', 
			'object_id' => 100, 
			'id' => 0, 
			'path' => '/0/', 
			'root_id' => 0,
			'ordr' => 100,
			'level' => 23,
			'parent_id' => 1000
		);
		
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'id'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'path'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'root_id'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'ordr'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'level'));
		
		$node_id = $this->driver->create_root_node($node);
		
		$this->assertNotIdentical($node_id, false);
		
		$this->db->sql_select(MATERIALIZED_PATH_TEST_TABLE);
		$arr = $this->db->get_array();
		$this->assertEqual(sizeof($arr), 1);
		
		$row = current($arr);
				
		$this->assertEqual($row['id'], $node_id, 'invalid parameter: id');
		$this->assertEqual($row['identifier'], 'test', 'invalid parameter: identifier');
		$this->assertEqual($row['level'], 1, 'invalid parameter: level');
		$this->assertEqual($row['ordr'], 1, 'invalid parameter: ordr');
		$this->assertEqual($row['parent_id'], 0, 'invalid parameter: parent_id');
		$this->assertEqual($row['root_id'], $node_id, 'invalid parameter: root_id');
		$this->assertEqual($row['path'], '/' . $node_id . '/', 'invalid parameter: path');
	} 
	
	function test_create_root_node_dumb()
	{
		$node = array(
			'identifier' => 'test', 
			'object_id' => 100, 
			'id' => 1000000000, 
			'path' => '/0/', 
			'root_id' => 0,
			'ordr' => 100,
			'level' => 23,
			'parent_id' => 1000
		);
		
		$this->driver->set_dumb_mode();
			
		$node_id = $this->driver->create_root_node($node);
		
		$this->assertEqual($node_id, 1000000000);
		
		$this->db->sql_select(MATERIALIZED_PATH_TEST_TABLE);
		$arr = $this->db->get_array();
		$this->assertEqual(sizeof($arr), 1);
		
		$row = current($arr);
				
		$this->assertEqual($row['id'], 1000000000, 'invalid parameter: id');
		$this->assertEqual($row['identifier'], 'test', 'invalid parameter: identifier');
		$this->assertEqual($row['level'], 1, 'invalid parameter: level');
		$this->assertEqual($row['ordr'], 1, 'invalid parameter: ordr');
		$this->assertEqual($row['parent_id'], 0, 'invalid parameter: parent_id');
		$this->assertEqual($row['root_id'], $node_id, 'invalid parameter: root_id');
		$this->assertEqual($row['path'], '/' . $node_id . '/', 'invalid parameter: path');
	} 

	function test_create_sub_node()
	{
		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		
		$parent_node = $this->driver->get_node($parent_node_id);
		
		$sub_node = array(
			'identifier' => 'test', 
			'object_id' => 100, 
			'id' => 0, 
			'path' => '/0/', 
			'root_id' => 0,
			'ordr' => 100,
			'level' => 23,
			'parent_id' => 1000
		);
		
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('parent_id' => 100000));
		$this->driver->create_sub_node(100000, $sub_node);
		
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'id'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'path'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'root_id'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'ordr'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'level'));

		$sub_node_id = $this->driver->create_sub_node($parent_node_id, $sub_node);

		$this->assertNotIdentical($sub_node_id, false);
		
		$this->db->sql_select(MATERIALIZED_PATH_TEST_TABLE);
		$arr = $this->db->get_array();
		$this->assertEqual(sizeof($arr), 2);
		
		$row = end($arr);

		$this->assertEqual($row['id'], $sub_node_id, 'invalid parameter: id');
		$this->assertEqual($row['identifier'], 'test', 'invalid parameter: identifier');
		$this->assertEqual($row['level'], 2, 'invalid parameter: level');
		$this->assertEqual($row['ordr'], 1, 'invalid parameter: ordr');
		$this->assertEqual($row['parent_id'], $parent_node_id, 'invalid parameter: parent_id');
		$this->assertEqual($row['root_id'], $parent_node['root_id'], 'invalid parameter: root_id');
		$this->assertEqual($row['path'], '/' . $parent_node_id . '/'. $sub_node_id . '/', 'invalid parameter: path');
	} 
	
	function test_create_sub_node_dumb()
	{
		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		
		$parent_node = $this->driver->get_node($parent_node_id);
		$this->driver->set_dumb_mode();
				
		$sub_node = array(
			'identifier' => 'test', 
			'object_id' => 100, 
			'id' => 12, 
			'path' => '/0/', 
			'root_id' => 0,
			'ordr' => 100,
			'level' => 23,
			'parent_id' => 1000
		);

		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('parent_id' => 100000));
		$this->driver->create_sub_node(100000, $sub_node);

		$sub_node_id = $this->driver->create_sub_node($parent_node_id, $sub_node);

		$this->assertNotIdentical($sub_node_id, false);
		$this->assertEqual($sub_node_id, 12);
		
		$this->db->sql_select(MATERIALIZED_PATH_TEST_TABLE);
		$arr = $this->db->get_array();
		$this->assertEqual(sizeof($arr), 2);
		
		$row = end($arr);

		$this->assertEqual($row['id'], $sub_node_id, 'invalid parameter: id');
		$this->assertEqual($row['identifier'], 'test', 'invalid parameter: identifier');
		$this->assertEqual($row['level'], 2, 'invalid parameter: level');
		$this->assertEqual($row['ordr'], 1, 'invalid parameter: ordr');
		$this->assertEqual($row['parent_id'], $parent_node_id, 'invalid parameter: parent_id');
		$this->assertEqual($row['root_id'], $parent_node['root_id'], 'invalid parameter: root_id');
		$this->assertEqual($row['path'], '/' . $parent_node_id . '/'. $sub_node_id . '/', 'invalid parameter: path');
	} 
		
	function test_delete_node()
	{
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('id' => 100000));
		$this->driver->delete_node(100000);

		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id1 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
		$sub_node_id2 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test2', 'object_id' => 20));
		$this->driver->create_sub_node($sub_node_id1, array('identifier' => 'test0', 'object_id' => 20));
		
		$this->driver->delete_node($sub_node_id1);
		
		$this->db->sql_select(MATERIALIZED_PATH_TEST_TABLE);
		$arr = $this->db->get_array();
		$this->assertEqual(sizeof($arr), 2);

		$row = end($arr);
		
		$this->assertEqual($row['id'], $sub_node_id2, 'invalid parameter: id');
		$this->assertEqual($row['identifier'], 'test2', 'invalid parameter: identifier');
		$this->assertEqual($row['level'], 2, 'invalid parameter: level');
		$this->assertEqual($row['ordr'], 1, 'invalid parameter: ordr');
		$this->assertEqual($row['parent_id'], $parent_node_id, 'invalid parameter: parent_id');
	}
	
	function test_create_left_node()
	{
		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
		$this->driver->create_sub_node($parent_node_id, array('identifier' => 'test2', 'object_id' => 20));
		$this->driver->create_sub_node($parent_node_id, array('identifier' => 'test3', 'object_id' => 20));
		$this->driver->create_sub_node($parent_node_id, array('identifier' => 'test4', 'object_id' => 20));
		
		$parent_node = $this->driver->get_node($parent_node_id);
		
		$left_node = array(
			'identifier' => 'left', 
			'object_id' => 100, 
			'id' => 0, 
			'path' => '/0/', 
			'root_id' => 0,
			'ordr' => 100,
			'level' => 23,
			'parent_id' => 1000
		);

		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('id' => 100000));
		$this->driver->create_right_node(100000, array('identifier' => 'left', 'object_id' => 100) );

		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'id'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'path'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'root_id'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'ordr'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'level'));

		$left_node_id = $this->driver->create_left_node($sub_node_id, $left_node);

		$this->assertNotIdentical($left_node_id, false);
		
		$this->db->sql_select(MATERIALIZED_PATH_TEST_TABLE);
		$arr = $this->db->get_array();
		$this->assertEqual(sizeof($arr), 6);
		
		$row = next($arr); //skipping root
		
		for($i=0; $i<4; $i++)
		{
			$this->assertEqual($row['ordr'], $i+2, 'invalid sibling parameter: ordr');
			$row = next($arr);
		}
		
		$row = end($arr);
		
		$this->assertEqual($row['id'], $left_node_id, 'invalid parameter: id');
		$this->assertEqual($row['identifier'], 'left', 'invalid parameter: identifier');
		$this->assertEqual($row['level'], 2, 'invalid parameter: level');
		$this->assertEqual($row['ordr'], 1, 'invalid parameter: ordr');
		$this->assertEqual($row['parent_id'], $parent_node_id, 'invalid parameter: parent_id');
		$this->assertEqual($row['root_id'], $parent_node['root_id'], 'invalid parameter: root_id');
		$this->assertEqual($row['path'], '/' . $parent_node_id . '/'. $left_node_id . '/', 'invalid parameter: path');
	}
	
	function test_create_right_node()
	{
		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
		$this->driver->create_sub_node($parent_node_id, array('identifier' => 'test2', 'object_id' => 20));
		$this->driver->create_sub_node($parent_node_id, array('identifier' => 'test3', 'object_id' => 20));
		$this->driver->create_sub_node($parent_node_id, array('identifier' => 'test4', 'object_id' => 20));
		
		$parent_node = $this->driver->get_node($parent_node_id);
		
		$right_node = array(
			'identifier' => 'right', 
			'object_id' => 100, 
			'id' => 0, 
			'path' => '/0/', 
			'root_id' => 0,
			'ordr' => 100,
			'level' => 23,
			'parent_id' => 1000
		);

		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('id' => 100000));
		$this->driver->create_right_node(100000, array('identifier' => 'right', 'object_id' => 100) );
		
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'id'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'path'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'root_id'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'ordr'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'level'));

		$right_node_id = $this->driver->create_right_node($sub_node_id, $right_node);

		$this->assertNotIdentical($right_node_id, false);
		
		$this->db->sql_select(MATERIALIZED_PATH_TEST_TABLE);
		$arr = $this->db->get_array();
		$this->assertEqual(sizeof($arr), 6);
		
		$row = next($arr); //skipping root
		
		for($i=0; $i<3; $i++)
		{
			$row = next($arr);
			$this->assertEqual($row['ordr'], $i+3, 'invalid sibling parameter: ordr');
		}
		
		$row = end($arr);
		
		$this->assertEqual($row['id'], $right_node_id, 'invalid parameter: id');
		$this->assertEqual($row['identifier'], 'right', 'invalid parameter: identifier');
		$this->assertEqual($row['level'], 2, 'invalid parameter: level');
		$this->assertEqual($row['ordr'], 2, 'invalid parameter: ordr');
		$this->assertEqual($row['parent_id'], $parent_node_id, 'invalid parameter: parent_id');
		$this->assertEqual($row['root_id'], $parent_node['root_id'], 'invalid parameter: root_id');
		$this->assertEqual($row['path'], '/' . $parent_node_id . '/'. $right_node_id . '/', 'invalid parameter: path');
	}
	
	function test_is_node()
	{
		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
		
		$this->assertTrue($this->driver->is_node($sub_node_id));
		$this->assertTrue($this->driver->is_node($parent_node_id));
		$this->assertFalse($this->driver->is_node(1000));
	}

	function test_get_parents()
	{
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('id' => 10000));
		$this->assertFalse($this->driver->get_parents(10000));
		
		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$this->driver->create_sub_node($parent_node_id, array('identifier' => 'test0', 'object_id' => 20));
		
		$sub_node_id1 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
		$sub_node_id2 = $this->driver->create_sub_node($sub_node_id1, array('identifier' => 'test2', 'object_id' => 20));

		$nodes = $this->driver->get_parents($sub_node_id2);
		
		$this->assertEqual(sizeof($nodes), 2);
		
		$row = reset($nodes);
		
		$this->assertEqual($row['id'], $parent_node_id, 'invalid parameter: id');
		$this->assertEqual($row['identifier'], 'root', 'invalid parameter: identifier');
		$this->assertEqual($row['object_id'], 10, 'invalid parameter: object_id');
		
		$row = end($nodes);

		$this->assertEqual($row['id'], $sub_node_id1, 'invalid parameter: id');
		$this->assertEqual($row['identifier'], 'test1', 'invalid parameter: identifier');
		$this->assertEqual($row['object_id'], 20, 'invalid parameter: object_id');

		$nodes = $this->driver->get_parents($sub_node_id1);
		
		$this->assertEqual(sizeof($nodes), 1);
	}

	function test_get_children()
	{
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('id' => 10000));
		$this->assertFalse($this->driver->get_children(10000));
		
		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id1 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
		$sub_node_id2 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test2', 'object_id' => 20));
		$this->driver->create_sub_node($sub_node_id1, array('identifier' => 'test0', 'object_id' => 20));
		
		$nodes = $this->driver->get_children($parent_node_id);
		
		$this->assertEqual(sizeof($nodes), 2);
		
		$row = reset($nodes);
		
		$this->assertEqual($row['id'], $sub_node_id1, 'invalid parameter: id');
		$this->assertEqual($row['identifier'], 'test1', 'invalid parameter: identifier');
		$this->assertEqual($row['object_id'], 20, 'invalid parameter: object_id');
		
		$row = end($nodes);

		$this->assertEqual($row['id'], $sub_node_id2, 'invalid parameter: id');
		$this->assertEqual($row['identifier'], 'test2', 'invalid parameter: identifier');
		$this->assertEqual($row['object_id'], 20, 'invalid parameter: object_id');
	}

	function test_count_children()
	{
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('id' => 10000));
		$this->assertFalse($this->driver->count_children(10000));
		
		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id1 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
		$sub_node_id2 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test2', 'object_id' => 20));
		$this->driver->create_sub_node($sub_node_id1, array('identifier' => 'test0', 'object_id' => 20));
		
		$this->assertEqual(2, $this->driver->count_children($parent_node_id));	
	}	
	
	function test_get_siblings()
	{
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('id' => 10000));
		$this->assertFalse($this->driver->get_siblings(10000));

		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id1 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
		$sub_node_id2 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test2', 'object_id' => 20));
		$this->driver->create_sub_node($sub_node_id1, array('identifier' => 'test0', 'object_id' => 20));
		
		$nodes = $this->driver->get_siblings($sub_node_id2);
		
		$this->assertEqual(sizeof($nodes), 2);
		
		$row = reset($nodes);
		
		$this->assertEqual($row['id'], $sub_node_id1, 'invalid parameter: id');
		$this->assertEqual($row['identifier'], 'test1', 'invalid parameter: identifier');
		$this->assertEqual($row['object_id'], 20, 'invalid parameter: object_id');
		
		$row = end($nodes);

		$this->assertEqual($row['id'], $sub_node_id2, 'invalid parameter: id');
		$this->assertEqual($row['identifier'], 'test2', 'invalid parameter: identifier');
		$this->assertEqual($row['object_id'], 20, 'invalid parameter: object_id');
	}
	
} 

?>