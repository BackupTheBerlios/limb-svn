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
require_once(LIMB_DIR . '/class/core/behaviours/site_object_behaviour.class.php');
require_once(LIMB_DIR . '/class/core/tree/tree_decorator.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('tree_decorator');
Mock :: generate('site_object_behaviour');

class site_object_behaviour_test_version extends site_object_behaviour
{	
	function _define_properties()
	{
		return array(
			'sort_order' => 3,
			'can_be_parent' => 1,
      'icon' => '/shared/images/folder.gif',
		);
	}
  
  public function define_action1($state_machine){}
  public function define_action2($state_machine){}
}

class site_object_behaviour_test extends LimbTestCase 
{ 
	var $db;
	var $object;
  var $behaviour;
  var $toolkit;
		 	
  function setUp()
  { 
  	$this->db = db_factory :: instance();
    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getDB', $this->db);
    
  	$this->_clean_up();
  	
  	$this->behaviour = new site_object_behaviour_test_version();  	
  }
  
  function tearDown()
  { 
  	$this->_clean_up();
    
    $this->toolkit->tally();
  }
  
  function _clean_up()
  {
  	$this->db->sql_delete('sys_behaviour');
    $this->db->sql_delete('sys_site_object');
    $this->db->sql_delete('sys_site_object_tree');
  }
  
  function test_get_actions_list()
  {
    $this->assertEqual(
                       array('action1', 'action2'),                       
                       $this->behaviour->get_actions_list());
  }

  function test_action_exists()
  {
    $this->assertTrue($this->behaviour->action_exists('action1'));
    $this->assertFalse($this->behaviour->action_exists('no_such_action'));
  }

  function test_get_property()
  {  	
  	$this->assertIdentical($this->behaviour->get_property('no_such_property', false), false);
  	
  	$this->assertEqual($this->behaviour->get_property('sort_order'), 3);
  }
  
  function test_get_behaviour_id()
  {
    // test auto create new record
    $id = $this->behaviour->get_id();
    
		$this->db->sql_select('sys_behaviour', '*', 'name="site_object_behaviour_test_version"');
		$arr = $this->db->fetch_row();
		
		$this->assertNotNull($id);
		
		$this->assertEqual($id, $arr['id']);
    $this->assertEqual($this->behaviour->get_property('icon'), $arr['icon']);
    $this->assertEqual($this->behaviour->get_property('can_be_parent'), $arr['can_be_parent']);
    $this->assertEqual($this->behaviour->get_property('sort_order'), $arr['sort_order']);
    
    // test only one record for one name
		$id = $this->behaviour->get_id();
		$this->db->sql_select('sys_behaviour', '*');
		$arr = $this->db->get_array();
		
		$this->assertEqual(sizeof($arr), 1);
	}
	
	function test_can_be_parent()
	{
		$this->assertTrue($this->behaviour->can_be_parent());
	}
  
  function test_can_accept_children_false()
  {
    $tree = new Mocktree_decorator($this);
    $tree->expectOnce('can_add_node', array(10));
    $tree->setReturnValue('can_add_node', false);
    
    $this->toolkit->setReturnValue('getTree', $tree);

    Limb :: registerToolkit($this->toolkit);
    
    $this->assertFalse(site_object_behaviour :: can_accept_children(10));
    
    $tree->tally();
    
    Limb :: popToolkit();    
  }
  
  function test_can_accept_children_true()
  { 
    $this->db->sql_insert('sys_behaviour', array('id' => $behaviour_id = 100,
                                                 'name' => 'test_behaviour'));
    $this->db->sql_insert('sys_behaviour', array('id' => 1000,
                                                 'name' => 'junk_behaviour'));
    
    
    $this->db->sql_insert('sys_site_object_tree', array('id' => $node_id = 10,
                                                        'root_id' => 1,
                                                        'identifier' => 'test_object',
                                                        'object_id' => $object_id = 20));

    $this->db->sql_insert('sys_site_object_tree', array('id' => 1000,
                                                        'root_id' => 1,
                                                        'identifier' => 'junk_object',
                                                        'object_id' => 200));

    $this->db->sql_insert('sys_site_object', array('id' => $object_id,
                                                   'class_id' => 1000,
                                                   'behaviour_id' => $behaviour_id,
                                                   'identifier' => 'test_object'));

    $tree = new Mocktree_decorator($this);
    $tree->expectOnce('can_add_node', array($node_id));
    $tree->setReturnValue('can_add_node', true);
    
    $mock_behaviour = new Mocksite_object_behaviour($this);
    $mock_behaviour->expectOnce('can_be_parent');
    $mock_behaviour->setReturnValue('can_be_parent', true);
    
    $this->toolkit->setReturnValue('getTree', $tree);
    $this->toolkit->expectOnce('createBehaviour', array('test_behaviour'));
    $this->toolkit->setReturnValue('createBehaviour', $mock_behaviour);
    
    Limb :: registerToolkit($this->toolkit);
    
    $this->assertTrue(site_object_behaviour :: can_accept_children($node_id));
    
    $mock_behaviour->tally();
    $tree->tally();
    
    Limb :: popToolkit();
  }
  
  function test_get_ids_by_names()
  {
    $this->db->sql_insert('sys_behaviour', array('id' => 10, 'name' => 'test1'));
    $this->db->sql_insert('sys_behaviour', array('id' => 11, 'name' => 'test2'));
    $this->db->sql_insert('sys_behaviour', array('id' => 12, 'name' => 'test3'));
    
    $ids = site_object_behaviour :: get_ids_by_names(array('test1', 'test2'));
    
    sort($ids);
    $this->assertEqual(sizeof($ids), 2);
    $this->assertEqual($ids, array(10, 11));
  }
}

?>