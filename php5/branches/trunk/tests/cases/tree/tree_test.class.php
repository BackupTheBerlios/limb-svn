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
require_once(LIMB_DIR . '/class/core/tree/tree.class.php');
require_once(LIMB_DIR . '/class/core/tree/drivers/tree_driver.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/session/session.class.php');

Mock :: generate('LimbToolkit'); 
Mock :: generate('tree_driver');
Mock :: generate('session');

class tree_test extends LimbTestCase
{
  var $tree;
  var $driver;
  var $toolkit;
  var $session;
  
  function setUp()
  {
    $this->session = new Mocksession($this);
    $this->toolkit = new MockLimbToolkit($this);
    
    $this->toolkit->setReturnValue('getSession', $this->session);
    
    $this->driver = new Mocktree_driver($this);
    $this->tree = new tree($this->driver);
    
    Limb :: registerToolkit($this->toolkit);
  }
  
  function tearDown()
  {
    $this->toolkit->tally();
    $this->driver->tally();
    $this->session->tally();
    
    Limb :: popToolkit();
  }
  
  function test_initialize_expanded_parents()
  {
    $parents = array(1, 2);
    
    $this->session->expectOnce('get_reference', array('tree_expanded_parents'));
    $this->session->setReturnReference('get_reference', $parents, array('tree_expanded_parents'));
    
    $parents = array(1, 2, 3);//testing reference was properly passed?
    
    $this->driver->expectOnce('set_expanded_parents', array($parents));
    
    $this->tree->initialize_expanded_parents();
  }
  
	function test_is_node()
	{
    $id = 100;
    $this->driver->expectOnce('is_node', array($id));
    $this->driver->setReturnValue('is_node', $res = 'whatever');
    $this->assertEqual($res, $this->tree->is_node($id));
	}

	function test_get_node()
	{
    $id = 100;
    $this->driver->expectOnce('get_node', array($id));
    $this->driver->setReturnValue('get_node', $res = 'whatever');
    $this->assertEqual($res, $this->tree->get_node($id));
	}

	function test_get_parent()
	{
    $id = 100;
    $this->driver->expectOnce('get_parent', array($id));
    $this->driver->setReturnValue('get_parent', $res = 'whatever');
    $this->assertEqual($res, $this->tree->get_parent($id));
	}

	function test_get_parents()
	{
    $id = 100;
    $this->driver->expectOnce('get_parents', array($id));
    $this->driver->setReturnValue('get_parents', $res = 'whatever');
    $this->assertEqual($res, $this->tree->get_parents($id));
	}

	function test_get_siblings()
	{
    $id = 100;
    $this->driver->expectOnce('get_siblings', array($id));
    $this->driver->setReturnValue('get_siblings', $res = 'whatever');
    $this->assertEqual($res, $this->tree->get_siblings($id));
	}

	function test_get_children()
	{
    $id = 100;
    $this->driver->expectOnce('get_children', array($id));
    $this->driver->setReturnValue('get_children', $res = 'whatever');
    $this->assertEqual($res, $this->tree->get_children($id));
	}

	function test_count_children()
	{
    $id = 100;
    $this->driver->expectOnce('count_children', array($id));
    $this->driver->setReturnValue('count_children', $res = 'whatever');
    $this->assertEqual($res, $this->tree->count_children($id));
	}

	function test_create_root_node()
	{
    $values = array('identifier' => 'test');
    $this->driver->expectOnce('create_root_node', array($values));
    $this->driver->setReturnValue('create_root_node', $res = 'whatever');
    $this->assertEqual($res, $this->tree->create_root_node($values));
	}
 
	function test_delete_node()
	{
    $id = 100;
    $this->driver->expectOnce('delete_node', array($id));
    $this->driver->setReturnValue('delete_node', $res = 'whatever');
    $this->assertEqual($res, $this->tree->delete_node($id));
	}

	function test_update_node()
	{
    $id = 100;
    $values = array('identifier' => 'test');
    $internal = true;
    
    $this->driver->expectOnce('update_node', array($id, $values, $internal));
    $this->driver->setReturnValue('update_node', $res = 'whatever');
    $this->assertEqual($res, $this->tree->update_node($id, $values, $internal));
	}

	function test_move_tree()
	{
    $id = 100;
    $target_id = 101;
    
    $this->driver->expectOnce('move_tree', array($id, $target_id));
    $this->driver->setReturnValue('move_tree', $res = 'whatever');
    $this->assertEqual($res, $this->tree->move_tree($id, $target_id));
	}

	function test_set_dumb_mode()
	{
    $status = true;
    $this->driver->expectOnce('set_dumb_mode', array($status));
    $this->tree->set_dumb_mode($status);
	}

	function test_get_all_nodes()
	{
    $this->driver->expectOnce('get_all_nodes');
    $this->driver->setReturnValue('get_all_nodes', $res = 'whatever');    
		$this->assertEqual($res, $this->tree->get_all_nodes());
	}

	function test_get_nodes_by_ids()
	{
    $ids_arr = array(100);
    $this->driver->expectOnce('get_nodes_by_ids', array($ids_arr));
    $this->driver->setReturnValue('get_nodes_by_ids', $res = 'whatever');
    $this->assertEqual($res, $this->tree->get_nodes_by_ids($ids_arr));
	}

	function test_get_max_child_identifier()
	{
    $id = 100;
    $this->driver->expectOnce('get_max_child_identifier', array($id));
    $this->driver->setReturnValue('get_max_child_identifier', $res = 'whatever');
    $this->assertEqual($res, $this->tree->get_max_child_identifier($id));
	}

	function test_get_node_by_path()
	{
    $path = 'test/path';
    $this->driver->expectOnce('get_node_by_path', array($path, '/'));
    $this->driver->setReturnValue('get_node_by_path', $res = 'whatever');
    $this->assertEqual($res, $this->tree->get_node_by_path($path));
	}

	function test_get_sub_branch()
	{
    $id = 100;
    $this->driver->expectOnce('get_sub_branch', array($id, -1, false, false));
    $this->driver->setReturnValue('get_sub_branch', $res = 'whatever');
    $this->assertEqual($res, $this->tree->get_sub_branch($id));
	}

	function test_get_sub_branch_by_path()
	{
    $path = '/test/path';
    $this->driver->expectOnce('get_sub_branch_by_path', array($path, -1, false, false));
    $this->driver->setReturnValue('get_sub_branch_by_path', $res = 'whatever');
    $this->assertEqual($res, $this->tree->get_sub_branch_by_path($path));
	}

	function test_get_root_nodes()
	{
    $this->driver->expectOnce('get_root_nodes');
    $this->driver->setReturnValue('get_root_nodes', $res = 'whatever');    
		$this->assertEqual($res, $this->tree->get_root_nodes());
	}

  function test_is_node_expanded()
  {
    $id = 100;
    $this->driver->expectOnce('is_node_expanded', array($id));
    $this->driver->setReturnValue('is_node_expanded', $res = 'whatever');
    $this->assertEqual($res, $this->tree->is_node_expanded($id));
  }

  function test_toggle_node()
  {
    $id = 100;
    $this->driver->expectOnce('toggle_node', array($id));
    $this->driver->setReturnValue('toggle_node', $res = 'whatever');
    $this->assertEqual($res, $this->tree->toggle_node($id));
  }

  function test_expand_node()
  {
    $id = 100;
    $this->driver->expectOnce('expand_node', array($id));
    $this->driver->setReturnValue('expand_node', $res = 'whatever');
    $this->assertEqual($res, $this->tree->expand_node($id));
  }

  function test_collapse_node()
  {
    $id = 100;
    $this->driver->expectOnce('collapse_node', array($id));
    $this->driver->setReturnValue('collapse_node', $res = 'whatever');
    $this->assertEqual($res, $this->tree->collapse_node($id));
  }
  
  function test_can_add_node_true()
  {
    $id = 100;
    $this->driver->expectOnce('is_node', array($id));
    $this->driver->setReturnValue('is_node', true);
    
    $this->assertTrue($this->tree->can_add_node($id));
  }
  
  function test_can_add_node_false()
  {
    $id = 100;
    $this->driver->expectOnce('is_node', array($id));
    $this->driver->setReturnValue('is_node', false);
    
    $this->assertFalse($this->tree->can_add_node($id));
  }   
  
  function test_can_delete_node_true()
  {
    $id = 100;
    $this->driver->expectOnce('count_children', array($id));
    $this->driver->setReturnValue('count_children', 0);
    
    $this->assertTrue($this->tree->can_delete_node($id));
  }

  function test_can_delete_node_true2()
  {
    $id = 100;
    $this->driver->expectOnce('count_children', array($id));
    $this->driver->setReturnValue('count_children', false);
    
    $this->assertTrue($this->tree->can_delete_node($id));
  }  
  
  function test_get_path_to_node_false()
  {
    $node = array('id' => 100);
    
    $this->driver->expectOnce('get_parents', array(100));
    $this->driver->setReturnValue('get_parents', false);
    
    $this->assertIdentical(false, $this->tree->get_path_to_node($node));
  }

  function test_get_path_to_node()
  {
    $node = array('id' => 100, 'identifier' => '3');
    
    $this->driver->expectOnce('get_parents', array(100));
    $this->driver->setReturnValue('get_parents', array(array('identifier' => '1'),
                                                       array('identifier' => '2')));
    $this->assertEqual('/1/2/3', $this->tree->get_path_to_node($node));
  }

  function test_get_path_to_node2()
  {
    $node = array('id' => 100, 'identifier' => '3');
    
    $this->driver->expectOnce('get_parents', array(100));
    $this->driver->setReturnValue('get_parents', array());
    $this->assertEqual('/3', $this->tree->get_path_to_node($node));
  }  
}

?> 
