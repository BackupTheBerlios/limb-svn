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
require_once(LIMB_DIR . '/class/core/tree/tree_decorator.class.php');
require_once(LIMB_DIR . '/class/core/tree/tree.interface.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');

Mock :: generate('LimbToolkit'); 
Mock :: generate('tree');

class tree_decorator_test extends LimbTestCase
{
  var $tree;
  var $driver;
  var $toolkit;
  
  function setUp()
  {
    $this->toolkit = new MockLimbToolkit($this);
    
    $this->tree = new Mocktree($this);
    $this->decorator = new tree_decorator($this->tree);
    
    Limb :: registerToolkit($this->toolkit);
  }
  
  function tearDown()
  {
    $this->toolkit->tally();
    $this->tree->tally();
    
    Limb :: popToolkit();
  }
    
	function test_is_node()
	{
    $id = 100;
    $this->tree->expectOnce('is_node', array($id));
    $this->tree->setReturnValue('is_node', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->is_node($id));
	}

	function test_get_node()
	{
    $id = 100;
    $this->tree->expectOnce('get_node', array($id));
    $this->tree->setReturnValue('get_node', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->get_node($id));
	}

	function test_get_parent()
	{
    $id = 100;
    $this->tree->expectOnce('get_parent', array($id));
    $this->tree->setReturnValue('get_parent', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->get_parent($id));
	}

	function test_get_parents()
	{
    $id = 100;
    $this->tree->expectOnce('get_parents', array($id));
    $this->tree->setReturnValue('get_parents', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->get_parents($id));
	}

	function test_get_siblings()
	{
    $id = 100;
    $this->tree->expectOnce('get_siblings', array($id));
    $this->tree->setReturnValue('get_siblings', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->get_siblings($id));
	}

	function test_get_children()
	{
    $id = 100;
    $this->tree->expectOnce('get_children', array($id));
    $this->tree->setReturnValue('get_children', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->get_children($id));
	}

	function test_count_children()
	{
    $id = 100;
    $this->tree->expectOnce('count_children', array($id));
    $this->tree->setReturnValue('count_children', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->count_children($id));
	}

	function test_create_root_node()
	{
    $values = array('identifier' => 'test');
    $this->tree->expectOnce('create_root_node', array($values));
    $this->tree->setReturnValue('create_root_node', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->create_root_node($values));
	}

  function test_create_sub_node()
	{
    $values = array('identifier' => 'test');
    $id = 100;
    $this->tree->expectOnce('create_sub_node', array($id, $values));
    $this->tree->setReturnValue('create_sub_node', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->create_sub_node($id, $values));
	}

	function test_delete_node()
	{
    $id = 100;
    $this->tree->expectOnce('delete_node', array($id));
    $this->tree->setReturnValue('delete_node', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->delete_node($id));
	}

	function test_update_node()
	{
    $id = 100;
    $values = array('identifier' => 'test');
    $internal = true;
    
    $this->tree->expectOnce('update_node', array($id, $values, $internal));
    $this->tree->setReturnValue('update_node', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->update_node($id, $values, $internal));
	}

	function test_move_tree()
	{
    $id = 100;
    $target_id = 101;
    
    $this->tree->expectOnce('move_tree', array($id, $target_id));
    $this->tree->setReturnValue('move_tree', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->move_tree($id, $target_id));
	}

	function test_set_dumb_mode()
	{
    $status = true;
    $this->tree->expectOnce('set_dumb_mode', array($status));
    $this->decorator->set_dumb_mode($status);
	}

	function test_get_all_nodes()
	{
    $this->tree->expectOnce('get_all_nodes');
    $this->tree->setReturnValue('get_all_nodes', $res = 'whatever');    
		$this->assertEqual($res, $this->decorator->get_all_nodes());
	}

	function test_get_nodes_by_ids()
	{
    $ids_arr = array(100);
    $this->tree->expectOnce('get_nodes_by_ids', array($ids_arr));
    $this->tree->setReturnValue('get_nodes_by_ids', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->get_nodes_by_ids($ids_arr));
	}

	function test_get_max_child_identifier()
	{
    $id = 100;
    $this->tree->expectOnce('get_max_child_identifier', array($id));
    $this->tree->setReturnValue('get_max_child_identifier', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->get_max_child_identifier($id));
	}

	function test_get_node_by_path()
	{
    $path = 'test/path';
    $this->tree->expectOnce('get_node_by_path', array($path, '/'));
    $this->tree->setReturnValue('get_node_by_path', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->get_node_by_path($path));
	}

	function test_get_sub_branch()
	{
    $id = 100;
    $this->tree->expectOnce('get_sub_branch', array($id, -1, false, false));
    $this->tree->setReturnValue('get_sub_branch', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->get_sub_branch($id));
	}

	function test_get_sub_branch_by_path()
	{
    $path = '/test/path';
    $this->tree->expectOnce('get_sub_branch_by_path', array($path, -1, false, false));
    $this->tree->setReturnValue('get_sub_branch_by_path', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->get_sub_branch_by_path($path));
	}

	function test_get_root_nodes()
	{
    $this->tree->expectOnce('get_root_nodes');
    $this->tree->setReturnValue('get_root_nodes', $res = 'whatever');    
		$this->assertEqual($res, $this->decorator->get_root_nodes());
	}

  function test_is_node_expanded()
  {
    $id = 100;
    $this->tree->expectOnce('is_node_expanded', array($id));
    $this->tree->setReturnValue('is_node_expanded', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->is_node_expanded($id));
  }

  function test_toggle_node()
  {
    $id = 100;
    $this->tree->expectOnce('toggle_node', array($id));
    $this->tree->setReturnValue('toggle_node', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->toggle_node($id));
  }

  function test_expand_node()
  {
    $id = 100;
    $this->tree->expectOnce('expand_node', array($id));
    $this->tree->setReturnValue('expand_node', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->expand_node($id));
  }

  function test_collapse_node()
  {
    $id = 100;
    $this->tree->expectOnce('collapse_node', array($id));
    $this->tree->setReturnValue('collapse_node', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->collapse_node($id));
  }
  
  function test_can_add_node_true()
  {
    $id = 100;
    $this->tree->expectOnce('is_node', array($id));
    $this->tree->setReturnValue('is_node', true);
    
    $this->assertTrue($this->decorator->can_add_node($id));
  }
  
  function test_can_add_node_false()
  {
    $id = 100;
    $this->tree->expectOnce('is_node', array($id));
    $this->tree->setReturnValue('is_node', false);
    
    $this->assertFalse($this->decorator->can_add_node($id));
  }   
  
  function test_can_delete_node_true()
  {
    $id = 100;
    $this->tree->expectOnce('count_children', array($id));
    $this->tree->setReturnValue('count_children', 0);
    
    $this->assertTrue($this->decorator->can_delete_node($id));
  }

  function test_can_delete_node_true2()
  {
    $id = 100;
    $this->tree->expectOnce('count_children', array($id));
    $this->tree->setReturnValue('count_children', false);
    
    $this->assertTrue($this->decorator->can_delete_node($id));
  }  
  
  function test_get_path_to_node_false()
  {
    $node = array('id' => 100);
    
    $this->tree->expectOnce('get_parents', array(100));
    $this->tree->setReturnValue('get_parents', false);
    
    $this->assertIdentical(false, $this->decorator->get_path_to_node($node));
  }

  function test_get_path_to_node()
  {
    $node = array('id' => 100, 'identifier' => '3');
    
    $this->tree->expectOnce('get_parents', array(100));
    $this->tree->setReturnValue('get_parents', array(array('identifier' => '1'),
                                                       array('identifier' => '2')));
    $this->assertEqual('/1/2/3', $this->decorator->get_path_to_node($node));
  }

  function test_get_path_to_node2()
  {
    $node = array('id' => 100, 'identifier' => '3');
    
    $this->tree->expectOnce('get_parents', array(100));
    $this->tree->setReturnValue('get_parents', array());
    $this->assertEqual('/3', $this->decorator->get_path_to_node($node));
  }  
}

?> 
