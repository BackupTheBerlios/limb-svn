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
require_once(LIMB_DIR . '/class/core/tree/drivers/nested_sets_driver.class.php');

define('NESTED_SETS_TEST_TABLE', 'test_nested_sets_tree');

class nested_sets_driver_test_version extends nested_sets_driver
{	
	var $_node_table = NESTED_SETS_TEST_TABLE;	
}

class nested_sets_driver_test extends LimbTestCase
{
	var $db = null;
	var $driver = null;
	
	function setUp()
	{
		$this->db = db_factory :: instance();
		
		debug_mock :: init($this);
		
		$this->driver = new nested_sets_driver_test_version();
		
		$this->_clean_up();
	} 

	function tearDown()
	{
		debug_mock :: tally();
		
		$this->_clean_up();
	} 
	
	function _clean_up()
	{
		$this->db->sql_delete(NESTED_SETS_TEST_TABLE);
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
			'l' => 1, 
			'r' => 2, 
			'root_id' => 10,
			'level' => 2,
			'parent_id' => 1000
		);

		$this->db->sql_insert(NESTED_SETS_TEST_TABLE, $node);
		
		$this->assertEqual($node, $this->driver->get_node(10));
	}
	
	
	function test_get_parent_failed()
	{
		$this->assertIdentical(false, $this->driver->get_parent(1000));
	}
	
	function test_get_parent()
	{
		$root_node = array(
			'identifier' => 'root', 
			'object_id' => 100, 
			'id' => 1, 
			'l' => 1, 
			'r' => 2, 
			'root_id' => 1,
			'level' => 1,
			'parent_id' => 0
		);

		$this->db->sql_insert(NESTED_SETS_TEST_TABLE, $root_node);

		$node = array(
			'identifier' => 'test', 
			'object_id' => 100, 
			'id' => 10, 
			'l' => 1, 
			'r' => 2, 
			'root_id' => 1,
			'level' => 2,
			'parent_id' => 1
		);

		$this->db->sql_insert(NESTED_SETS_TEST_TABLE, $node);
		
		$this->assertEqual($root_node, $this->driver->get_parent(10));
		$this->assertIdentical(false, $this->driver->get_parent(1));		
	}
		
	function test_create_root_node()
	{
		$node = array(
			'identifier' => 'test', 
			'object_id' => 100, 
			'id' => 0, 
			'l' => -1000, 
			'r' => 1000,
			'root_id' => 0,
			'level' => 23,
			'parent_id' => 1000
		);
		
		$node_id = $this->driver->create_root_node($node);
		
		$this->assertNotIdentical($node_id, false);
		
		$this->db->sql_select(NESTED_SETS_TEST_TABLE);
		$arr = $this->db->get_array();
		$this->assertEqual(sizeof($arr), 1);
		
		$row = current($arr);
				
		$this->assertEqual($row['id'], $node_id, 'invalid parameter: id');
		$this->assertEqual($row['object_id'], 100, 'invalid parameter: object_id');
		$this->assertEqual($row['identifier'], 'test', 'invalid parameter: identifier');
		$this->assertEqual($row['level'], 1, 'invalid parameter: level');
		$this->assertEqual($row['parent_id'], 0, 'invalid parameter: parent_id');
		$this->assertEqual($row['root_id'], $node_id, 'invalid parameter: root_id');
		$this->assertEqual($row['l'], 1, 'invalid parameter: l');
		$this->assertEqual($row['r'], 2, 'invalid parameter: r');
	} 
	
	function test_create_root_node_dumb()
	{
		$node = array(
			'identifier' => 'test', 
			'object_id' => 100, 
			'id' => 1000000000, 
			'l' => -1000, 
			'r' => 1000,
			'root_id' => 0,
			'level' => 23,
			'parent_id' => 1000
		);
		
		$this->driver->set_dumb_mode();
			
		$node_id = $this->driver->create_root_node($node);
		
		$this->assertEqual($node_id, 1000000000);
		
		$this->db->sql_select(NESTED_SETS_TEST_TABLE);
		$arr = $this->db->get_array();
		$row = current($arr);
				
		$this->assertEqual($row['id'], 1000000000, 'invalid parameter: id');
	} 
	
	function test_create_sub_node_failed()
	{
		$this->driver->create_sub_node(100000, array());
	}

	function test_create_sub_node_no_children_in_parent()
	{
		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		
		$parent_node = $this->driver->get_node($parent_node_id);
		
		$sub_node = array(
			'identifier' => 'test', 
			'object_id' => 100, 
			'id' => 0, 
			'l' => 1, 
			'r' => 2, 
			'root_id' => 0,
			'level' => 23,
			'parent_id' => 1000
		);
				
		$sub_node_id = $this->driver->create_sub_node($parent_node_id, $sub_node);

		$this->assertNotIdentical($sub_node_id, false);
		
		$this->db->sql_select(NESTED_SETS_TEST_TABLE);
		$arr = $this->db->get_array();
		$this->assertEqual(sizeof($arr), 2);
		
		$row = reset($arr);
		$this->assertEqual($row['l'], 1, 'invalid parameter: l');
		$this->assertEqual($row['r'], 4, 'invalid parameter: r');
		
		$row = end($arr);

		$this->assertEqual($row['id'], $sub_node_id, 'invalid parameter: id');
		$this->assertEqual($row['object_id'], 100, 'invalid parameter: object_id');
		$this->assertEqual($row['identifier'], 'test', 'invalid parameter: identifier');
		$this->assertEqual($row['level'], 2, 'invalid parameter: level');
		$this->assertEqual($row['parent_id'], $parent_node_id, 'invalid parameter: parent_id');
		$this->assertEqual($row['root_id'], $parent_node['root_id'], 'invalid parameter: root_id');
		$this->assertEqual($row['l'], 2, 'invalid parameter: l');
		$this->assertEqual($row['r'], 3, 'invalid parameter: r');
	} 
	
	function test_create_sub_node_with_children_in_parent()
	{
		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id1 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test', 'object_id' => 20));
		$sub_node_id1_1 = $this->driver->create_sub_node($sub_node_id1, array('identifier' => 'test', 'object_id' => 20));
		$sub_node_id1_2 = $this->driver->create_sub_node($sub_node_id1, array('identifier' => 'test', 'object_id' => 20));
		
		$parent_node = $this->driver->get_node($parent_node_id);
		
		$sub_node = array(
			'identifier' => 'test', 
			'object_id' => 100, 
		);
				
		$sub_node_id = $this->driver->create_sub_node($sub_node_id1, $sub_node);

		$this->assertNotIdentical($sub_node_id, false);
		
		$this->db->sql_select(NESTED_SETS_TEST_TABLE);
		$arr = $this->db->get_array();
		$this->assertEqual(sizeof($arr), 5);
		
		$row = reset($arr);
		$this->assertEqual($row['l'], 1, 'invalid parameter: l');
		$this->assertEqual($row['r'], 10, 'invalid parameter: r');

		$row = next($arr);
		$this->assertEqual($row['l'], 2, 'invalid parameter: l');
		$this->assertEqual($row['r'], 9, 'invalid parameter: r');
		
		$row = end($arr);

		$this->assertEqual($row['id'], $sub_node_id, 'invalid parameter: id');
		$this->assertEqual($row['object_id'], 100, 'invalid parameter: object_id');
		$this->assertEqual($row['identifier'], 'test', 'invalid parameter: identifier');
		$this->assertEqual($row['level'], 3, 'invalid parameter: level');
		$this->assertEqual($row['parent_id'], $sub_node_id1, 'invalid parameter: parent_id');
		$this->assertEqual($row['root_id'], $parent_node['root_id'], 'invalid parameter: root_id');
		$this->assertEqual($row['l'], 7, 'invalid parameter: l');
		$this->assertEqual($row['r'], 8, 'invalid parameter: r');
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
		);

		$sub_node_id = $this->driver->create_sub_node($parent_node_id, $sub_node);

		$this->assertNotIdentical($sub_node_id, false);
		$this->assertEqual($sub_node_id, 12);
		
		$this->db->sql_select(NESTED_SETS_TEST_TABLE);
		$arr = $this->db->get_array();
		$row = end($arr);

		$this->assertEqual($row['id'], $sub_node_id, 'invalid parameter: id');
	} 

	function test_get_max_identifier_failed()
	{
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
		$this->driver->delete_node(100000);
	}
			
	function test_delete_node()
	{
		$parent_node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		$sub_node_id1 = $this->driver->create_sub_node($parent_node_id, array('identifier' => 'test', 'object_id' => 20));
		$sub_node_id1_1 = $this->driver->create_sub_node($sub_node_id1, array('identifier' => 'test', 'object_id' => 20));
		$sub_node_id1_2 = $this->driver->create_sub_node($sub_node_id1, array('identifier' => 'test', 'object_id' => 20));
		
		$this->driver->delete_node($sub_node_id1_1);
		
		$this->db->sql_select(NESTED_SETS_TEST_TABLE);
		$arr = $this->db->get_array();
		$this->assertEqual(sizeof($arr), 3);
		
		$row = reset($arr);

		$this->assertEqual($row['id'], $parent_node_id, 'invalid parameter: id');
		$this->assertEqual($row['l'], 1, 'invalid parameter: l');
		$this->assertEqual($row['r'], 6, 'invalid parameter: r');
		
		$row = next($arr);

		$this->assertEqual($row['id'], $sub_node_id1, 'invalid parameter: id');
		$this->assertEqual($row['l'], 2, 'invalid parameter: l');
		$this->assertEqual($row['r'], 5, 'invalid parameter: r');
		
		$row = next($arr);
		
		$this->assertEqual($row['id'], $sub_node_id1_2, 'invalid parameter: id');
		$this->assertEqual($row['l'], 3, 'invalid parameter: l');
		$this->assertEqual($row['r'], 4, 'invalid parameter: r');
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
		$this->_check_proper_nesting_simple($nodes, __LINE__);
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
		$this->_check_proper_nesting_simple($nodes, __LINE__);
		$this->_check_result_nodes_array($nodes, __LINE__);
	}
	
	function test_get_children_failed()
	{
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
		$this->assertFalse($this->driver->update_node(10000, array()));
	}
	
	function test_update_node()
	{
		$node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
		
		$node = array(
			'identifier' => 'test', 
			'object_id' => 100, 
			'id' => 12, 
			'l' => 1, 
			'r' => 2, 
			'root_id' => 0,
			'level' => 23,
			'parent_id' => 1000
		);

		$this->assertTrue($this->driver->update_node($node_id, $node));
		
		$updated_node = $this->driver->get_node($node_id);
		
		$this->assertEqual($updated_node['object_id'], 100, 'invalid parameter: object_id');
		$this->assertEqual($updated_node['identifier'], 'test', 'invalid parameter: identifier');
		$this->assertNotEqual($updated_node, $node, 'invalid update');
	}
//	
//	function test_move_tree_failed()
//	{
//		debug_mock :: expect_write_error(tree_driver :: TREE_ERROR_RECURSION,  array('id' => 1, 'target_id' => 1));
//		$this->assertFalse($this->driver->move_tree(1, 1));
//		
//		debug_mock :: expect_write_error(tree_driver :: TREE_ERROR_NODE_NOT_FOUND,  array('id' => 1));
//		$this->assertFalse($this->driver->move_tree(1, 2));
//		
//		$node_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
//		$sub_node_id = $this->driver->create_sub_node($node_id, array('identifier' => 'test', 'object_id' => 10));
//		
//		debug_mock :: expect_write_error(tree_driver :: TREE_ERROR_NODE_NOT_FOUND,  array('target_id' => $node_id-1));
//		$this->assertFalse($this->driver->move_tree($node_id, $node_id-1));
//
//		debug_mock :: expect_write_error(tree_driver :: TREE_ERROR_RECURSION,  array('id' => $node_id, 'target_id' => $sub_node_id));
//		$this->assertFalse($this->driver->move_tree($node_id, $sub_node_id));
//	}
//	
//	function test_move_tree()
//	{
//		$root_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
//		$sub_node_id_1 = $this->driver->create_sub_node($root_id, array('identifier' => 'test', 'object_id' => 10));
//		$sub_node_id_1_1 = $this->driver->create_sub_node($sub_node_id_1, array('identifier' => 'test', 'object_id' => 10));
//		$sub_node_id_1_1_1 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 10));
//		$sub_node_id_1_1_2 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 10));
//		
//		$root_id_2 = $this->driver->create_root_node( array('identifier' => 'test', 'object_id' => 10));
//		$sub_node_id_2 = $this->driver->create_sub_node($root_id_2, array('identifier' => 'test', 'object_id' => 10));
//		
//		$this->assertTrue($this->driver->move_tree($sub_node_id_1, $sub_node_id_2));
//		
//		$current_path = '/' . $root_id_2 . '/' . $sub_node_id_2 . '/';
//		
//		$sub_node_1 = $this->driver->get_node($sub_node_id_1);
//		
//		$current_path .= $sub_node_id_1 . '/';
//		$this->assertEqual($sub_node_1['level'], 3, 'invalid parameter: level');
//		$this->assertEqual($sub_node_1['parent_id'], $sub_node_id_2, 'invalid parameter: parent_id');
//		$this->assertEqual($sub_node_1['path'], $current_path, 'invalid parameter: path');
//		$this->assertEqual($sub_node_1['root_id'], $root_id_2, 'invalid parameter: root_id');
//		
//		$current_path .= $sub_node_id_1_1 . '/';
//		$sub_node_1_1 = $this->driver->get_node($sub_node_id_1_1);
//		
//		$this->assertEqual($sub_node_1_1['level'], 4, 'invalid parameter: level');
//		$this->assertEqual($sub_node_1_1['parent_id'], $sub_node_id_1, 'invalid parameter: parent_id');
//		$this->assertEqual($sub_node_1_1['path'], $current_path , 'invalid parameter: path');
//		$this->assertEqual($sub_node_1_1['root_id'], $root_id_2, 'invalid parameter: root_id');
//
//		$sub_node_1_1_1 = $this->driver->get_node($sub_node_id_1_1_1);
//		
//		$this->assertEqual($sub_node_1_1_1['level'], 5, 'invalid parameter: level');
//		$this->assertEqual($sub_node_1_1_1['parent_id'], $sub_node_id_1_1, 'invalid parameter: parent_id');
//		$this->assertEqual($sub_node_1_1_1['path'], $current_path . $sub_node_id_1_1_1 . '/', 'invalid parameter: path');
//		$this->assertEqual($sub_node_1_1_1['root_id'], $root_id_2, 'invalid parameter: root_id');
//
//		$sub_node_1_1_2 = $this->driver->get_node($sub_node_id_1_1_2);
//		
//		$this->assertEqual($sub_node_1_1_2['level'], 5, 'invalid parameter: level');
//		$this->assertEqual($sub_node_1_1_2['parent_id'], $sub_node_id_1_1, 'invalid parameter: parent_id');
//		$this->assertEqual($sub_node_1_1_2['path'], $current_path . $sub_node_id_1_1_2 . '/', 'invalid parameter: path');
//		$this->assertEqual($sub_node_1_1_2['root_id'], $root_id_2, 'invalid parameter: root_id');
//	}
//	
	function test_get_sub_branch_failed()
	{
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
		$this->_check_proper_nesting_strict($branch, -1, __LINE__);

		$node = current($branch);
		$this->assertEqual($node['id'], $sub_node_id_1_1, 'invalid parameter: id');
		
		//getting at unlimited depth, including node
		$branch = $this->driver->get_sub_branch($sub_node_id_1, -1, true);
		$this->assertEqual(4, sizeof($branch));
		$this->_check_result_nodes_array($branch, __LINE__);
		$this->_check_proper_nesting_strict($branch, -1, __LINE__);
		
		//getting at depth = 1
		$branch = $this->driver->get_sub_branch($sub_node_id_1, 1);
		$this->assertEqual(1, sizeof($branch));
		$this->_check_result_nodes_array($branch,  __LINE__);
		$this->_check_proper_nesting_strict($branch, 1, __LINE__);
		
		//getting at depth = 1, including node
		$branch = $this->driver->get_sub_branch($sub_node_id_1, 1, true);
		$this->assertEqual(2, sizeof($branch));
		$this->_check_result_nodes_array($branch,  __LINE__);
		$this->_check_proper_nesting_strict($branch, 2, __LINE__);
	}
//	
//	function test_get_sub_branch_only_parents()
//	{
//		$this->db->sql_insert('sys_site_object', array('id' => 10, 'class_id' => 100));
//		$this->db->sql_insert('sys_site_object', array('id' => 20, 'class_id' => 200));
//		$this->db->sql_insert('sys_class', array('id' => 100, 'can_be_parent' => 1));
//		$this->db->sql_insert('sys_class', array('id' => 200, 'can_be_parent' => 0));
//
//		$root_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
//		$sub_node_id_1 = $this->driver->create_sub_node($root_id, array('identifier' => 'test', 'object_id' => 10));
//		$sub_node_id_1_1 = $this->driver->create_sub_node($sub_node_id_1, array('identifier' => 'test', 'object_id' => 10));
//		$sub_node_id_1_1_1 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 20));
//		$sub_node_id_1_1_2 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 20));
//		$sub_node_id_2 = $this->driver->create_sub_node($root_id, array('identifier' => 'test', 'object_id' => 20));
//		$sub_node_id_1_2 = $this->driver->create_sub_node($sub_node_id_1, array('identifier' => 'test', 'object_id' => 20));
//		
//		//getting at depth = 1, including node, not checking expanded parents, only parents
//		$branch = $this->driver->get_sub_branch($root_id, 1, true, false, true); 
//		$this->assertEqual(2, sizeof($branch));
//		$this->_check_result_nodes_array($branch, __LINE__);
//		$this->_check_proper_nesting($branch, __LINE__);
//		
//		//getting at unlimited depth, including node, not checking expanded parents, only parents
//		$branch = $this->driver->get_sub_branch($root_id, -1, true, false, true); 
//		$this->assertEqual(3, sizeof($branch));
//		$this->_check_result_nodes_array($branch, __LINE__);
//		$this->_check_proper_nesting($branch, __LINE__);
//	}
//	
//	function test_get_sub_branch_check_expanded_parents()
//	{
//		$this->db->sql_insert('sys_site_object', array('id' => 10, 'class_id' => 100));
//		$this->db->sql_insert('sys_site_object', array('id' => 20, 'class_id' => 200));
//		$this->db->sql_insert('sys_class', array('id' => 100, 'can_be_parent' => 1));
//		$this->db->sql_insert('sys_class', array('id' => 200, 'can_be_parent' => 0));
//
//		$root_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
//		$sub_node_id_1 = $this->driver->create_sub_node($root_id, array('identifier' => 'test', 'object_id' => 10));
//		$sub_node_id_1_1 = $this->driver->create_sub_node($sub_node_id_1, array('identifier' => 'test', 'object_id' => 10));
//		$sub_node_id_1_1_1 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 20));
//		$sub_node_id_1_1_2 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 20));
//		
//		$this->driver->check_expanded_parents();
//
//		$this->driver->expand_node($sub_node_id_1);
//
//		//getting at unlimited depth, including node, checking expanded parents
//		$branch = $this->driver->get_sub_branch($root_id, -1, true, true); 
//		$this->assertEqual(3, sizeof($branch));
//		$this->_check_result_nodes_array($branch, __LINE__);
//		$this->_check_proper_nesting($branch, __LINE__);
//		
//		$this->driver->collapse_node($root_id);
//		$this->driver->expand_node($sub_node_id_1_1);
//		
//		//getting at unlimited depth, including node, checking expanded parents
//		$branch = $this->driver->get_sub_branch($root_id, -1, true, true); 
//		$this->assertEqual(1, sizeof($branch));
//		$this->_check_result_nodes_array($branch, __LINE__);
//		$this->_check_proper_nesting($branch, __LINE__);
//	}
//	
//	function test_get_node_by_path_failed()
//	{
//		$root_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
//		$sub_node_id_1 = $this->driver->create_sub_node($root_id, array('identifier' => '1_test', 'object_id' => 10));
//		$sub_node_id_1_1 = $this->driver->create_sub_node($sub_node_id_1, array('identifier' => '1_1_test', 'object_id' => 20));
//		$sub_node_id_1_1_1 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => '1_1_1_test', 'object_id' => 10));
//		$sub_node_id_1_1_2 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => '1_1_2_test', 'object_id' => 10));
//		
//		$root_id2 = $this->driver->create_root_node(array('identifier' => 'root2', 'object_id' => 10));
//		$sub_node_id_2 = $this->driver->create_sub_node($root_id2, array('identifier' => '2_test', 'object_id' => 10));
//
//		$this->assertFalse($this->driver->get_node_by_path(''));
//		$this->assertFalse($this->driver->get_node_by_path('/root///'));
//		$this->assertFalse($this->driver->get_node_by_path('/root/wow/yo'));
//		$this->assertFalse($this->driver->get_node_by_path('/root/2_test'));
//	}
//		
//	function test_get_node_by_path()
//	{
//		$root_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
//		$sub_node_id_1 = $this->driver->create_sub_node($root_id, array('identifier' => 'test1', 'object_id' => 10));
//		$sub_node_id_1_1 = $this->driver->create_sub_node($sub_node_id_1, array('identifier' => 'test1', 'object_id' => 20));
//		$sub_node_id_1_1_1 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test1', 'object_id' => 10));
//		$sub_node_id_1_1_2 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test2', 'object_id' => 10));
//
//		$node = $this->driver->get_node_by_path('/root/');
//		$this->assertEqual($node['id'], $root_id);
//		$this->_check_result_nodes_array($node, __LINE__);
//
//		$node = $this->driver->get_node_by_path('/root/test1/test1/');
//		$this->assertEqual($node['id'], $sub_node_id_1_1);
//		$this->_check_result_nodes_array($node,  __LINE__);
//		
//		$node = $this->driver->get_node_by_path('/root/test1/test1/test2');
//		$this->assertEqual($node['id'], $sub_node_id_1_1_2);
//		$this->_check_result_nodes_array($node,  __LINE__);
//		
//		$node = $this->driver->get_node_by_path('/root/test1/test1/test1/');
//		$this->assertEqual($node['id'], $sub_node_id_1_1_1);
//		$this->_check_result_nodes_array($node,  __LINE__);
//	}
//
//	function test_get_sub_branch_by_path_failed()
//	{
//		debug_mock :: expect_write_error(TREE_ERROR_NODE_NOT_FOUND,  array('id' => 1));
//		$this->assertFalse($this->driver->get_sub_branch(1));
//	}
//	
//	function test_get_nodes_by_ids()
//	{
//		$root_id = $this->driver->create_root_node(array('identifier' => 'root', 'object_id' => 10));
//		$sub_node_id_1 = $this->driver->create_sub_node($root_id, array('identifier' => 'test1', 'object_id' => 10));
//		$sub_node_id_1_1 = $this->driver->create_sub_node($sub_node_id_1, array('identifier' => 'test1', 'object_id' => 20));
//		$sub_node_id_1_1_1 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test1', 'object_id' => 10));
//		$sub_node_id_1_1_2 = $this->driver->create_sub_node($sub_node_id_1_1, array('identifier' => 'test2', 'object_id' => 10));
//		
//		$nodes = $this->driver->get_nodes_by_ids(
//			array(
//				$root_id, 
//				$sub_node_id_1,
//				$sub_node_id_1_1, 
//				$sub_node_id_1_1_2, 
//				$sub_node_id_1_1_1,
//				-1
//			)
//		);
//		
//		$this->assertEqual(sizeof($nodes), 5);
//		$this->_check_result_nodes_array($nodes,  __LINE__);
//	}
//	
	function _check_result_nodes_array($nodes, $line='')
	{
		if(isset($nodes['object_id']))
			$this->assertEqual($this->driver->get_node($nodes['id']), $nodes, 'at line: ' . $line);
		else
			foreach($nodes as $id => $node)
				$this->assertEqual($this->driver->get_node($id), $node, 'at line: ' . $line);
	}
	
	function _check_proper_nesting_simple($nodes, $line='')
	{
		$prev_l = -1000000;
		foreach($nodes as $node)
		{
			$this->assertTrue($node['l'] > $prev_l, 
				'l is invalid: ' . $node['l'] . ' , expected greater than: ' . $prev_l . ' at line: ' . $line);
				
			$prev_l = $node['l'];
		}
	}
	
	function _check_proper_nesting_strict($nodes, $level_limit=-1, $line='')
	{
		$l = complex_array :: get_min_column_value('l', $nodes, $index);
		$r = complex_array :: get_max_column_value('r', $nodes, $index);
		$node = current($nodes);
		
		$this->assertEqual($node['l'], $l, 
			'l is invalid: ' . $node['l'] . ' , expected : ' . $l . ' at line: ' . $line);

		$this->assertEqual($node['r'], $r, 
			'r is invalid: ' . $node['r'] . ' , expected : ' . $r . ' at line: ' . $line);
		
		$children = ($r - $l - 1)/2;
		
		if($children > 0)
		{
			$last_r = $this->_check_proper_nesting_recursive($nodes, $line, $l, $r, $children, $level_limit-1);
			
			if($last_r !== false && sizeof($nodes) > $level_limit)
				$this->assertEqual($node['r'] - $last_r, 1,
					'there is a gap between r ' . $node['r'] . ' and r ' . $last_r . ' at line: ' . $line);
		}
	}
	
	function _check_proper_nesting_recursive(&$nodes, $line, $l, $r, $c, $level_limit)
	{
		if(($current = next($nodes)) === false)
			return false;
		
		for($i=0; $i < $c; $i++)
		{
			$children = ($current['r'] - $current['l'] - 1) / 2;

			$this->assertTrue($current['l'] > $l, 
				'l is invalid: ' . $current['l'] . ' , expected greater than: ' . $l . ' at line: ' . $line);
	
			$this->assertTrue($current['r'] < $r, 
				'r is invalid: ' . $current['r'] . ' , expected less than: ' . $r . ' at line: ' . $line);
				
			$current_r = $current['r'];
			
			if($children > 0 && ($level_limit <= -1 || $level_limit > 0 ))
			{
				$last_r = $this->_check_proper_nesting_recursive($nodes, $line, $current['l'], $current['r'], $children, $level_limit-1);
				$c = $c - $children - 1;
				
				if($last_r !== false)
					$this->assertEqual($current['r'] - $last_r, 1,
						'there is a gap between r ' . $current['r'] . ' and r ' . $last_r . ' at line: ' . $line);
			}
			else
				$current = next($nodes);
		}
		
		return $current_r;
	}
	
} 
?>