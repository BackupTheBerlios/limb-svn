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
require_once(LIMB_DIR . 'class/core/tree/drivers/materialized_path_driver.class.php');

define('MATERIALIZED_PATH_TEST_TABLE', 'test_materialized_path_tree');

class materialized_path_driver_test_version extends materialized_path_driver
{	
	var $_node_table = MATERIALIZED_PATH_TEST_TABLE;	
}

class materialized_path_driver_test extends LimbTestCase
{
	var $db = null;
	var $driver = null;
	
	function setUp()
	{
		$this->db = db_factory :: instance();
		
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
		$this->db->sql_delete('sys_site_object');
		$this->db->sql_delete('sys_class');
	}
	
	function test_get_node_failed()
	{
		$this->assertIdentical(false, $this->driver->get_node(10000));
	}
	
	function test_get_node()
	{
		$node = array(
			'identifier' => 'test', 
			'object_id' => 100, 
			'id' => 10, 
			'path' => '/10/', 
			'root_id' => 10,
			'level' => 2,
			'parent_id' => 1000,
			'children' => 0
		);

		$this->db->sql_insert(MATERIALIZED_PATH_TEST_TABLE, $node);
		
		$this->assertEqual($node, $this->driver->get_node(10));
	}
		
	function test_get_parent_failed()
	{
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('id' => 1000));
		$this->assertIdentical(false, $this->driver->get_parent(1000));
	}
	
	function test_get_parent()
	{
		$root_node = array(
			'identifier' => 'root', 
			'object_id' => 100, 
			'id' => 1, 
			'path' => '/1/', 
			'root_id' => 1,
			'level' => 1,
			'parent_id' => 0,
			'children' => 1
		);

		$this->db->sql_insert(MATERIALIZED_PATH_TEST_TABLE, $root_node);

		$node = array(
			'identifier' => 'test', 
			'object_id' => 100, 
			'id' => 10, 
			'path' => '/1/10/', 
			'root_id' => 1,
			'level' => 2,
			'parent_id' => 1,
			'children' => 0
		);

		$this->db->sql_insert(MATERIALIZED_PATH_TEST_TABLE, $node);
		
		$this->assertEqual($root_node, $this->driver->get_parent(10));
		$this->assertIdentical(false, $this->driver->get_parent(1));		
	}
		
	function test_create_root_node()
	{
		$node = array(
			'identifier' => 'test', 
			'object_id' => 100, 
			'id' => 0, 
			'path' => '/0/', 
			'root_id' => 0,
			'level' => 23,
			'parent_id' => 1000,
			'children' => 1000
		);
		
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'id'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'path'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'root_id'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'level'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'children'));
		
		$node_id = $this->driver->create_root_node($node);
		
		$this->assertNotIdentical($node_id, false);
		
		$this->db->sql_select(MATERIALIZED_PATH_TEST_TABLE);
		$arr = $this->db->get_array();
		$this->assertEqual(sizeof($arr), 1);
		
		$row = current($arr);
				
		$this->assertEqual($row['id'], $node_id, 'invalid parameter: id');
		$this->assertEqual($row['object_id'], 100, 'invalid parameter: object_id');
		$this->assertEqual($row['identifier'], 'test', 'invalid parameter: identifier');
		$this->assertEqual($row['level'], 1, 'invalid parameter: level');
		$this->assertEqual($row['parent_id'], 0, 'invalid parameter: parent_id');
		$this->assertEqual($row['root_id'], $node_id, 'invalid parameter: root_id');
		$this->assertEqual($row['path'], '/' . $node_id . '/', 'invalid parameter: path');
		$this->assertEqual($row['children'], 0, 'invalid parameter: children');
	} 
	
	function test_create_root_node_dumb()
	{
		$node = array(
			'identifier' => 'test', 
			'object_id' => 100, 
			'id' => 1000000000, 
			'path' => '/0/', 
			'root_id' => 0,
			'level' => 23,
			'parent_id' => 1000,
			'children' => 10000
		);
		
		$this->driver->set_dumb_mode();
			
		$node_id = $this->driver->create_root_node($node);
		
		$this->assertEqual($node_id, 1000000000);
		
		$this->db->sql_select(MATERIALIZED_PATH_TEST_TABLE);
		$arr = $this->db->get_array();
		$row = current($arr);
				
		$this->assertEqual($row['id'], 1000000000, 'invalid parameter: id');
	} 

	function test_create_sub_node_failed()
	{
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('parent_id' => 100000));
		$this->driver->create_sub_node(100000, array());
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
			'level' => 23,
			'parent_id' => 1000,
			'children' => 1000
		);
				
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'id'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'path'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'root_id'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'level'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'children'));

		$sub_node_id = $this->driver->create_sub_node($parent_node_id, $sub_node);

		$this->assertNotIdentical($sub_node_id, false);
		
		$this->db->sql_select(MATERIALIZED_PATH_TEST_TABLE);
		$arr = $this->db->get_array();
		$this->assertEqual(sizeof($arr), 2);
		
		$row = reset($arr);
		
		$this->assertEqual($row['children'], 1, 'invalid parameter in parent: children');
		
		$row = end($arr);

		$this->assertEqual($row['id'], $sub_node_id, 'invalid parameter: id');
		$this->assertEqual($row['object_id'], 100, 'invalid parameter: object_id');
		$this->assertEqual($row['identifier'], 'test', 'invalid parameter: identifier');
		$this->assertEqual($row['level'], 2, 'invalid parameter: level');
		$this->assertEqual($row['parent_id'], $parent_node_id, 'invalid parameter: parent_id');
		$this->assertEqual($row['root_id'], $parent_node['root_id'], 'invalid parameter: root_id');
		$this->assertEqual($row['path'], '/' . $parent_node_id . '/'. $sub_node_id . '/', 'invalid parameter: path');
		$this->assertEqual($row['children'], 0, 'invalid parameter: children');
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
			'level' => 23,
			'parent_id' => 1000,
			'children' => 1000
		);

		$sub_node_id = $this->driver->create_sub_node($parent_node_id, $sub_node);

		$this->assertNotIdentical($sub_node_id, false);
		$this->assertEqual($sub_node_id, 12);
		
		$this->db->sql_select(MATERIALIZED_PATH_TEST_TABLE);
		$arr = $this->db->get_array();
		$row = end($arr);

		$this->assertEqual($row['id'], $sub_node_id, 'invalid parameter: id');
	} 
	
	function test_get_max_identifier_failed()
	{
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('id' => 1000));
		$this->assertIdentical(false, $this->driver->get_max_child_identifier(1000));
	}
	
	function test_get_max_identifier()
	{
		$root_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		
		$this->assertEqual(0, $this->driver->get_max_child_identifier($root_id));
		
		$sub_node_id_1_1 = $this->driver->create_sub_node($root_id, array('identifier' => 'test1', 'object_id' => 20));
		$sub_node_id_1_2 = $this->driver->create_sub_node($root_id, array('identifier' => 'test3', 'object_id' => 10));
		$sub_node_id_1_3 = $this->driver->create_sub_node($root_id, array('identifier' => 'test2', 'object_id' => 10));
		
		$this->assertEqual('test3', $this->driver->get_max_child_identifier($root_id));
	}
	
	function test_get_max_identifier_natural_sort()
	{
		$root_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		
		$this->assertEqual(0, $this->driver->get_max_child_identifier($root_id));
		
		$sub_node_id_1_1 = $this->driver->create_sub_node($root_id, array('identifier' => 'test8', 'object_id' => 20));
		$sub_node_id_1_2 = $this->driver->create_sub_node($root_id, array('identifier' => 'test9', 'object_id' => 10));
		$sub_node_id_1_3 = $this->driver->create_sub_node($root_id, array('identifier' => 'test10', 'object_id' => 10));
		
		$this->assertEqual('test10', $this->driver->get_max_child_identifier($root_id));
	}	

	function test_delete_node_failed()
	{
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('id' => 100000));
		$this->driver->delete_node(100000);
	}
			
	function test_delete_node()
	{
		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id1 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
		$sub_node_id2 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test2', 'object_id' => 20));
		$this->driver->create_sub_node($sub_node_id1, array('identifier' => 'test0', 'object_id' => 20));
		
		$this->driver->delete_node($sub_node_id1);
		
		$this->db->sql_select(MATERIALIZED_PATH_TEST_TABLE);
		$arr = $this->db->get_array();
		$this->assertEqual(sizeof($arr), 2);

		$row = reset($arr);
		
		$this->assertEqual($row['children'], 1, 'invalid parent parameter: children');
		
		$row = end($arr);
		
		$this->assertEqual($row['id'], $sub_node_id2, 'invalid parameter: id');
		$this->assertEqual($row['object_id'], 20, 'invalid parameter: object_id');
		$this->assertEqual($row['identifier'], 'test2', 'invalid parameter: identifier');
		$this->assertEqual($row['level'], 2, 'invalid parameter: level');
		$this->assertEqual($row['parent_id'], $parent_node_id, 'invalid parameter: parent_id');
		$this->assertEqual($row['children'], 0, 'invalid parameter: children');
	}
	
	function test_is_node()
	{
		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
		
		$this->assertTrue($this->driver->is_node($sub_node_id));
		$this->assertTrue($this->driver->is_node($parent_node_id));
		$this->assertFalse($this->driver->is_node(1000));
	}
	
	function test_get_parents_failed()
	{
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('id' => 10000));
		$this->assertFalse($this->driver->get_parents(10000));
	}

	function test_get_parents()
	{
		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$this->driver->create_sub_node($parent_node_id, array('identifier' => 'test0', 'object_id' => 20));
		
		$sub_node_id1 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
		$sub_node_id2 = $this->driver->create_sub_node($sub_node_id1, array('identifier' => 'test2', 'object_id' => 20));

		$nodes = $this->driver->get_parents($sub_node_id2);
		
		$this->assertEqual(sizeof($nodes), 2);
		$this->_check_proper_nesting($nodes, __LINE__);
		$this->_check_result_nodes_array($nodes, __LINE__);
		
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
		$this->_check_proper_nesting($nodes, __LINE__);
		$this->_check_result_nodes_array($nodes, __LINE__);
	}
	
	function test_get_children_failed()
	{
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('id' => 10000));
		$this->assertFalse($this->driver->get_children(10000));
	}
	
	function test_get_children()
	{		
		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id1 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
		$sub_node_id2 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test2', 'object_id' => 20));
		$this->driver->create_sub_node($sub_node_id1, array('identifier' => 'test0', 'object_id' => 20));
		
		$nodes = $this->driver->get_children($parent_node_id);
		
		$this->assertEqual(sizeof($nodes), 2);
		$this->_check_result_nodes_array($nodes, __LINE__);
		
		$row = reset($nodes);
		
		$this->assertEqual($row['id'], $sub_node_id1, 'invalid parameter: id');
		$this->assertEqual($row['identifier'], 'test1', 'invalid parameter: identifier');
		$this->assertEqual($row['object_id'], 20, 'invalid parameter: object_id');
		
		$row = end($nodes);

		$this->assertEqual($row['id'], $sub_node_id2, 'invalid parameter: id');
		$this->assertEqual($row['identifier'], 'test2', 'invalid parameter: identifier');
		$this->assertEqual($row['object_id'], 20, 'invalid parameter: object_id');
	}
	
	function test_count_children_failed()
	{
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('id' => 10000));
		$this->assertFalse($this->driver->count_children(10000));
	}

	function test_count_children()
	{		
		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id1 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
		$sub_node_id2 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test2', 'object_id' => 20));
		$this->driver->create_sub_node($sub_node_id1, array('identifier' => 'test0', 'object_id' => 20));
		
		$this->assertEqual(2, $this->driver->count_children($parent_node_id));	
	}	
	
	function test_get_siblings_failed()
	{
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('id' => 10000));
		$this->assertFalse($this->driver->get_siblings(10000));		
	}
	
	function test_get_siblings()
	{
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
	
	function test_update_node_failed()
	{
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND, array('id' => 10000));
		$this->assertFalse($this->driver->update_node(10000, array()));
	}
	
	function test_update_node()
	{
		$node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		
		$node = array(
			'identifier' => 'test', 
			'object_id' => 100, 
			'id' => 12, 
			'path' => '/0/', 
			'root_id' => 0,
			'level' => 23,
			'parent_id' => 1000,
			'children' => 1000
		);

		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'id'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'path'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'root_id'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'level'));
		debug_mock :: expect_write_error(TREE_ERROR_NODE_WRONG_PARAM, array('value' => 'children'));

		$this->assertTrue($this->driver->update_node($node_id, $node));
		
		$updated_node = $this->driver->get_node($node_id);
		
		$this->assertEqual($updated_node['object_id'], 100, 'invalid parameter: object_id');
		$this->assertEqual($updated_node['identifier'], 'test', 'invalid parameter: identifier');
		$this->assertNotEqual($updated_node, $node, 'invalid update');
	}
	
	function test_move_tree_failed()
	{
		debug_mock :: expect_write_error(TREE_ERROR_RECURSION,  array('id' => 1, 'target_id' => 1));
		$this->assertFalse($this->driver->move_tree(1, 1));
		
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND,  array('id' => 1));
		$this->assertFalse($this->driver->move_tree(1, 2));
		
		$node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id = $this->driver->create_sub_node($node_id, array('identifier' => 'test', 'object_id' => 10));
		
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND,  array('target_id' => $node_id-1));
		$this->assertFalse($this->driver->move_tree($node_id, $node_id-1));

		debug_mock :: expect_write_error(TREE_ERROR_RECURSION,  array('id' => $node_id, 'target_id' => $sub_node_id));
		$this->assertFalse($this->driver->move_tree($node_id, $sub_node_id));
	}
	
	function test_move_tree()
	{
		$root_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id_1 = $this->driver->create_sub_node($root_id, array('identifier' => 'test', 'object_id' => 10));
		$sub_node_id_1_1 = $this->driver->create_sub_node($sub_node_id_1, array('identifier' => 'test', 'object_id' => 10));
		$sub_node_id_1_1_1 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 10));
		$sub_node_id_1_1_2 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 10));
		
		$root_id_2 = $this->driver->create_root_node( array('identifier' => 'test', 'object_id' => 10));
		$sub_node_id_2 = $this->driver->create_sub_node($root_id_2, array('identifier' => 'test', 'object_id' => 10));
		
		$root_node = $this->driver->get_node($root_id);		
		$this->assertEqual($root_node['children'], 1, 'invalid parent parameter: children');
		
		$this->assertTrue($this->driver->move_tree($sub_node_id_1, $sub_node_id_2));

		$root_node = $this->driver->get_node($root_id);		
		$this->assertEqual($root_node['children'], 0, 'invalid parent parameter: children');
		
		$sub_node_2 = $this->driver->get_node($sub_node_id_2);
		$this->assertEqual($sub_node_2['children'], 1, 'invalid parent parameter: children');
		
		$current_path = '/' . $root_id_2 . '/' . $sub_node_id_2 . '/';
		
		$sub_node_1 = $this->driver->get_node($sub_node_id_1);
		
		$current_path .= $sub_node_id_1 . '/';
		$this->assertEqual($sub_node_1['level'], 3, 'invalid parameter: level');
		$this->assertEqual($sub_node_1['parent_id'], $sub_node_id_2, 'invalid parameter: parent_id');
		$this->assertEqual($sub_node_1['path'], $current_path, 'invalid parameter: path');
		$this->assertEqual($sub_node_1['root_id'], $root_id_2, 'invalid parameter: root_id');
		
		$current_path .= $sub_node_id_1_1 . '/';
		$sub_node_1_1 = $this->driver->get_node($sub_node_id_1_1);
		
		$this->assertEqual($sub_node_1_1['level'], 4, 'invalid parameter: level');
		$this->assertEqual($sub_node_1_1['parent_id'], $sub_node_id_1, 'invalid parameter: parent_id');
		$this->assertEqual($sub_node_1_1['path'], $current_path , 'invalid parameter: path');
		$this->assertEqual($sub_node_1_1['root_id'], $root_id_2, 'invalid parameter: root_id');

		$sub_node_1_1_1 = $this->driver->get_node($sub_node_id_1_1_1);
		
		$this->assertEqual($sub_node_1_1_1['level'], 5, 'invalid parameter: level');
		$this->assertEqual($sub_node_1_1_1['parent_id'], $sub_node_id_1_1, 'invalid parameter: parent_id');
		$this->assertEqual($sub_node_1_1_1['path'], $current_path . $sub_node_id_1_1_1 . '/', 'invalid parameter: path');
		$this->assertEqual($sub_node_1_1_1['root_id'], $root_id_2, 'invalid parameter: root_id');

		$sub_node_1_1_2 = $this->driver->get_node($sub_node_id_1_1_2);
		
		$this->assertEqual($sub_node_1_1_2['level'], 5, 'invalid parameter: level');
		$this->assertEqual($sub_node_1_1_2['parent_id'], $sub_node_id_1_1, 'invalid parameter: parent_id');
		$this->assertEqual($sub_node_1_1_2['path'], $current_path . $sub_node_id_1_1_2 . '/', 'invalid parameter: path');
		$this->assertEqual($sub_node_1_1_2['root_id'], $root_id_2, 'invalid parameter: root_id');
	}
	
	function test_get_sub_branch_failed()
	{
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND,  array('id' => 1));
		$this->assertFalse($this->driver->get_sub_branch(1));
	}

	function test_get_sub_branch()
	{
		$root_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id_1 = $this->driver->create_sub_node($root_id, array('identifier' => 'test', 'object_id' => 10));
		$sub_node_id_1_1 = $this->driver->create_sub_node($sub_node_id_1, array('identifier' => 'test', 'object_id' => 20));
		$sub_node_id_1_1_1 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 10));
		$sub_node_id_1_1_2 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 10));
		
		//getting all		
		$branch = $this->driver->get_sub_branch($sub_node_id_1);
		$this->assertEqual(3, sizeof($branch));
		$this->_check_result_nodes_array($branch, __LINE__);
		$this->_check_proper_nesting($branch, __LINE__);

		$node = current($branch);
		$this->assertEqual($node['id'], $sub_node_id_1_1, 'invalid parameter: id');
		
		//getting at unlimited depth, including node
		$branch = $this->driver->get_sub_branch($sub_node_id_1, -1, true);
		$this->assertEqual(4, sizeof($branch));
		$this->_check_result_nodes_array($branch, __LINE__);
		$this->_check_proper_nesting($branch, __LINE__);
		
		//getting at depth = 1
		$branch = $this->driver->get_sub_branch($sub_node_id_1, 1);
		$this->assertEqual(1, sizeof($branch));
		$this->_check_result_nodes_array($branch,  __LINE__);
		$this->_check_proper_nesting($branch, __LINE__);
		
		//getting at depth = 1, including node
		$branch = $this->driver->get_sub_branch($sub_node_id_1, 1, true);
		$this->assertEqual(2, sizeof($branch));
		$this->_check_result_nodes_array($branch,  __LINE__);
		$this->_check_proper_nesting($branch, __LINE__);
	}
	
	function test_get_sub_branch_only_parents()
	{
		$this->db->sql_insert('sys_site_object', array('id' => 10, 'class_id' => 100));
		$this->db->sql_insert('sys_site_object', array('id' => 20, 'class_id' => 200));
		$this->db->sql_insert('sys_class', array('id' => 100, 'can_be_parent' => 1));
		$this->db->sql_insert('sys_class', array('id' => 200, 'can_be_parent' => 0));

		$root_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id_1 = $this->driver->create_sub_node($root_id, array('identifier' => 'test', 'object_id' => 10));
		$sub_node_id_1_1 = $this->driver->create_sub_node($sub_node_id_1, array('identifier' => 'test', 'object_id' => 10));
		$sub_node_id_1_1_1 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 20));
		$sub_node_id_1_1_2 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 20));
		$sub_node_id_2 = $this->driver->create_sub_node($root_id, array('identifier' => 'test', 'object_id' => 20));
		$sub_node_id_1_2 = $this->driver->create_sub_node($sub_node_id_1, array('identifier' => 'test', 'object_id' => 20));
		
		//getting at depth = 1, including node, not checking expanded parents, only parents
		$branch = $this->driver->get_sub_branch($root_id, 1, true, false, true); 
		$this->assertEqual(2, sizeof($branch));
		$this->_check_result_nodes_array($branch, __LINE__);
		$this->_check_proper_nesting($branch, __LINE__);
		
		//getting at unlimited depth, including node, not checking expanded parents, only parents
		$branch = $this->driver->get_sub_branch($root_id, -1, true, false, true); 
		$this->assertEqual(3, sizeof($branch));
		$this->_check_result_nodes_array($branch, __LINE__);
		$this->_check_proper_nesting($branch, __LINE__);
	}
	
	function test_get_sub_branch_check_expanded_parents()
	{
		$this->db->sql_insert('sys_site_object', array('id' => 10, 'class_id' => 100));
		$this->db->sql_insert('sys_site_object', array('id' => 20, 'class_id' => 200));
		$this->db->sql_insert('sys_class', array('id' => 100, 'can_be_parent' => 1));
		$this->db->sql_insert('sys_class', array('id' => 200, 'can_be_parent' => 0));

		//creating subtree
		$root_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id_1 = $this->driver->create_sub_node($root_id, array('identifier' => 'test', 'object_id' => 10));
		$sub_node_id_1_1 = $this->driver->create_sub_node($sub_node_id_1, array('identifier' => 'test', 'object_id' => 10));
		$sub_node_id_1_1_1 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 20));
		$sub_node_id_1_1_2 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 20));
		
		//creating second subtree to check that only one subtree uses during fetch subbranch
		$root_id_2 = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id_2 = $this->driver->create_sub_node($root_id_2, array('identifier' => 'test', 'object_id' => 10));
		$sub_node_id_2_1 = $this->driver->create_sub_node($sub_node_id_2, array('identifier' => 'test', 'object_id' => 10));
		$sub_node_id_2_1_1 = $this->driver->create_sub_node($sub_node_id_2_1, array('identifier' => 'test', 'object_id' => 20));
		$sub_node_id_2_1_2 = $this->driver->create_sub_node($sub_node_id_2_1, array('identifier' => 'test', 'object_id' => 20));

		$this->driver->check_expanded_parents();

		$this->driver->expand_node($sub_node_id_1);

		$this->driver->expand_node($sub_node_id_2);
		$this->driver->expand_node($sub_node_id_2_1);

		//getting at unlimited depth, including node, checking expanded parents
		$branch = $this->driver->get_sub_branch($root_id, -1, true, true); 
		$this->assertEqual(3, sizeof($branch));
		$this->_check_result_nodes_array($branch, __LINE__);
		$this->_check_proper_nesting($branch, __LINE__);
		
		$this->driver->collapse_node($root_id);
		$this->driver->expand_node($sub_node_id_1_1);
		
		//getting at unlimited depth, including node, checking expanded parents
		$branch = $this->driver->get_sub_branch($root_id, -1, true, true); 
		$this->assertEqual(1, sizeof($branch));
		$this->_check_result_nodes_array($branch, __LINE__);
		$this->_check_proper_nesting($branch, __LINE__);
	}
	
	function test_get_node_by_path_failed()
	{
		$root_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id_1 = $this->driver->create_sub_node($root_id, array('identifier' => '1_test', 'object_id' => 10));
		$sub_node_id_1_1 = $this->driver->create_sub_node($sub_node_id_1, array('identifier' => '1_1_test', 'object_id' => 20));
		$sub_node_id_1_1_1 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => '1_1_1_test', 'object_id' => 10));
		$sub_node_id_1_1_2 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => '1_1_2_test', 'object_id' => 10));
		
		$root_id2 = $this->driver->create_root_node(array('identifier' => 'root2', 'object_id' => 10));
		$sub_node_id_2 = $this->driver->create_sub_node($root_id2, array('identifier' => '2_test', 'object_id' => 10));

		$this->assertFalse($this->driver->get_node_by_path(''));
		$this->assertFalse($this->driver->get_node_by_path('/root///'));
		$this->assertFalse($this->driver->get_node_by_path('/root/wow/yo'));
		$this->assertFalse($this->driver->get_node_by_path('/root/2_test'));
	}
		
	function test_get_node_by_path()
	{
		$root_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id_1 = $this->driver->create_sub_node($root_id, array('identifier' => 'test1', 'object_id' => 10));
		$sub_node_id_1_1 = $this->driver->create_sub_node($sub_node_id_1, array('identifier' => 'test1', 'object_id' => 20));
		$sub_node_id_1_1_1 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test1', 'object_id' => 10));
		$sub_node_id_1_1_2 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test2', 'object_id' => 10));

		$node = $this->driver->get_node_by_path('/root/');
		$this->assertEqual($node['id'], $root_id);
		$this->_check_result_nodes_array($node, __LINE__);

		$node = $this->driver->get_node_by_path('/root/test1/test1/');
		$this->assertEqual($node['id'], $sub_node_id_1_1);
		$this->_check_result_nodes_array($node,  __LINE__);
		
		$node = $this->driver->get_node_by_path('/root/test1/test1/test2');
		$this->assertEqual($node['id'], $sub_node_id_1_1_2);
		$this->_check_result_nodes_array($node,  __LINE__);
		
		$node = $this->driver->get_node_by_path('/root/test1/test1/test1/');
		$this->assertEqual($node['id'], $sub_node_id_1_1_1);
		$this->_check_result_nodes_array($node,  __LINE__);
	}

	function test_get_sub_branch_by_path_failed()
	{
		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND,  array('id' => 1));
		$this->assertFalse($this->driver->get_sub_branch(1));
	}
	
	function test_get_nodes_by_ids()
	{
		$root_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id_1 = $this->driver->create_sub_node($root_id, array('identifier' => 'test1', 'object_id' => 10));
		$sub_node_id_1_1 = $this->driver->create_sub_node($sub_node_id_1, array('identifier' => 'test1', 'object_id' => 20));
		$sub_node_id_1_1_1 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test1', 'object_id' => 10));
		$sub_node_id_1_1_2 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test2', 'object_id' => 10));
		
		$nodes = $this->driver->get_nodes_by_ids(
			array(
				$root_id, 
				$sub_node_id_1,
				$sub_node_id_1_1, 
				$sub_node_id_1_1_2, 
				$sub_node_id_1_1_1,
				-1
			)
		);
		
		$this->assertEqual(sizeof($nodes), 5);
		$this->_check_result_nodes_array($nodes,  __LINE__);

		$nodes = $this->driver->get_nodes_by_ids(
			array(
				$sub_node_id_1,
				$sub_node_id_1_1, 
				$sub_node_id_1_1_1,
				-1
			)
		);
		
		$this->assertEqual(sizeof($nodes), 3);
		$this->_check_result_nodes_array($nodes,  __LINE__);

		$nodes = $this->driver->get_nodes_by_ids(
			array()
		);
		
		$this->assertEqual(sizeof($nodes), 0);
		$this->_check_result_nodes_array($nodes,  __LINE__);

	}
	
	function _check_result_nodes_array($nodes, $line='')
	{
		if(isset($nodes['object_id']))
			$this->assertEqual($this->driver->get_node($nodes['id']), $nodes, 'at line: ' . $line);
		else
			foreach($nodes as $id => $node)
				$this->assertEqual($this->driver->get_node($id), $node, 'at line: ' . $line);
	}
	
	function _check_proper_nesting($nodes, $line='')
	{
		$paths[] = complex_array :: get_min_column_value('path', $nodes, $index);
		
		$counter = 0;
		foreach($nodes as $id => $node)
		{
			if($counter == 0)
			{
				$this->assertEqual($node['path'], $paths[0], 
					'first element path is invalid: ' . $node['path'] . ' , expected : ' . $paths[0] . ' at line: ' . $line);
			}
			elseif(preg_match('~^(.*/)[^/]+/$~', $node['path'], $matches))
			{
				$prev_path = $matches[1];
				$this->assertTrue(in_array($prev_path, $paths), 
					'path is improperly nested: ' . $node['path'] . ' , expected parent not found: ' . $prev_path . ' at line: ' . $line);
			}
			else
			{
				$this->assertFalse(true, 'path is invalid: ' . $node['path'] . ' at line: ' . $line);
			}
			
			$paths[] = $node['path'];
			$counter++;
		}
	}
	
} 
?>