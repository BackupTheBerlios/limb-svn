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
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object_factory.class.php');
require_once(LIMB_DIR . '/class/core/behaviours/site_object_behaviour.class.php');
require_once(LIMB_DIR . '/class/core/base_limb_toolkit.class.php');
require_once(LIMB_DIR . '/class/core/tree/tree.class.php');
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');

Mock::generatePartial('BaseLimbToolkit',
                      'SiteObjectToolkitMock', array());

class SiteObjectManipulationTestToolkit extends SiteObjectToolkitMock
{
  var $_mocked_methods = array('getTree', 'getUser');
  
  public function getTree() 
  { 
    $args = func_get_args();
    return $this->_mock->_invoke('getTree', $args); 
  } 
  
  public function getUser() 
  { 
    $args = func_get_args();
    return $this->_mock->_invoke('getUser', $args); 
  }
}

Mock::generate('tree');
Mock::generate('user');

Mock::generatePartial('site_object',
                      'site_object_manipulation_test_version',
                      array('_can_add_node_to_parent', 
                            'get_class_id',
                            '_can_delete_site_object'));

class site_object_manipulation_test extends LimbTestCase 
{ 
	var $db;
	var $object;
  
  var $toolkit;
  var $tree;
  var $user;
  
  function setUp()
  {
    $this->toolkit = new SiteObjectManipulationTestToolkit($this);
    $this->tree = new Mocktree($this);
    $this->user = new Mockuser($this);
    $this->user->setReturnValue('get_id', 125);
    
    $this->toolkit->setReturnValue('getTree', $this->tree);
    $this->toolkit->setReturnValue('getUser', $this->user);
    
    Limb :: registerToolkit($this->toolkit);
    
  	$this->db = db_factory :: instance();
  	
  	$this->_clean_up();
  	
  	$this->object = new site_object_manipulation_test_version($this);
    $this->object->setReturnValue('get_class_id', 10);
    $this->object->__construct();  	
  }
  
  function tearDown()
  { 
  	$this->_clean_up();
  	
    $this->toolkit->tally();
    $this->tree->tally();
    $this->object->tally();
    
    Limb :: popToolkit();
  }
  
  function _clean_up()
  {
  	$this->db->sql_delete('sys_site_object');
  }
  
  function test_failed_create_no_identifier()
  {
  	try
  	{
  	  $this->object->create();
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'identifier is empty');
  	}
  }

  function test_failed_create_no_behaviour_id()
  {
		$this->object->set_identifier('test');
    
  	try
  	{
  	  $this->object->create();
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'behaviour_id is not set');
  	}
  }

  function test_failed_create_parent_id_not_set()
  {	
		$this->object->set_identifier('test');
		$this->object->set_behaviour_id($behaviour_id = 1);

  	try
  	{
  	  $this->object->create();
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'tree parent node is empty');
  	}
  }
  
  function test_failed_create_cant_register_node()
  {	
  	$this->object->set_parent_node_id($parent_node_id = 10);
		$this->object->set_identifier('test');
		$this->object->set_behaviour_id($behaviour_id = 1);
		$this->object->expectOnce('_can_add_node_to_parent', array($parent_node_id));
		$this->object->setReturnValue('_can_add_node_to_parent', false);
		 
  	try
  	{
  	  $this->object->create();
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'tree registering failed');
  	  $this->assertEqual($e->getAdditionalParams(), array('parent_node_id' => 10));
  	}
  }

  function test_failed_create_root_node_create_failed()
  {	
		$this->object->set_identifier('test');
		$this->object->set_behaviour_id($behaviour_id = 1);

		$this->tree->expectOnce('create_root_node');
    $this->tree->setReturnValue('create_root_node', false);
		$this->object->expectNever('_can_add_node_to_parent');
    
  	try
  	{
  	  $this->object->create(true);
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'could not create root tree node');
  	}
  }
  
  function test_create_ok()
  {
  	$this->object->set_parent_node_id($parent_node_id = 10);
  	$this->object->set_identifier('node_test');
		$this->object->set_behaviour_id($behaviour_id = 25);

		$this->object->setReturnValue('_can_add_node_to_parent', true);
		$this->tree->expectOnce('create_sub_node');
    $this->tree->setReturnValue('create_sub_node', $node_id = 200);

  	$id = $this->object->create();
  	
  	$this->assertEqual($id, $this->object->get_id());
    $this->assertEqual($node_id, $this->object->get_node_id());

  	$this->_check_sys_site_object_record();
  }

  function test_create_ok_is_root()
  {
  	$this->object->set_parent_node_id($parent_node_id = 10);
  	$this->object->set_identifier('node_test');
		$this->object->set_behaviour_id($behaviour_id = 25);

		$this->tree->expectOnce('create_root_node');
    $this->tree->setReturnValue('create_root_node', $node_id = 200);
		$this->object->expectNever('_can_add_node_to_parent');

  	$id = $this->object->create(true);
  	
  	$this->assertEqual($id, $this->object->get_id());
    $this->assertEqual($node_id, $this->object->get_node_id());

  	$this->_check_sys_site_object_record();
  }
  
  function  test_update_failed_no_id()
  {
  	try
  	{
  	  $this->object->update();
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'object id not set');
  	}
  }
  
  function  test_update_failed_no_node_id()
  {
    $this->object->set_id(1);
  	try
  	{
  	  $this->object->update();
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'node id not set');
  	}
  }

  function  test_update_failed_no_parent_node_id()
  {
    $this->object->set_id(1);
    $this->object->set_node_id(10);
  	try
  	{
  	  $this->object->update();
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'parent node id not set');
  	}
  }

  function  test_update_failed_no_behaviour_id()
  {
    $this->object->set_id(1);
    $this->object->set_node_id(10);
    $this->object->set_parent_node_id(100);
  	try
  	{
  	  $this->object->update();
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'behaviour id not set');
  	}
  }

  function  test_update_failed_no_identifier()
  {
    $this->object->set_id(1);
    $this->object->set_node_id(10);
    $this->object->set_parent_node_id(100);
    $this->object->set_behaviour_id(1000);
  	try
  	{
  	  $this->object->update();
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'identifier is empty');
  	}
  }

  function  test_update_failed_to_move()
  {
    $this->object->set_id(1);
    $this->object->set_node_id($node_id = 10);
    $this->object->set_parent_node_id($parent_node_id = 100);
    $this->object->set_behaviour_id(1000);
    $this->object->set_identifier($identifier = 'test');
    
    $this->object->setReturnValue('_can_add_node_to_parent', true, array($parent_node_id));
    
    $this->tree->expectOnce('get_node');
    $this->tree->setReturnValue('get_node', array('parent_id' => 110), array($node_id));
    
    $this->tree->expectOnce('move_tree');
    $this->tree->setReturnValue('move_tree', false, array($node_id, $parent_node_id));
    
  	try
  	{
  	  $this->object->update();
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'could not move node');
  	}
  }

  function  test_update_failed_new_parent_cant_accept_children()
  {
    $this->object->set_id(1);
    $this->object->set_node_id($node_id = 10);
    $this->object->set_parent_node_id($parent_node_id = 100);
    $this->object->set_behaviour_id(1000);
    $this->object->set_identifier($identifier = 'test');
    
    $this->object->setReturnValue('_can_add_node_to_parent', false, array($parent_node_id));
    
    $this->tree->expectOnce('get_node');
    $this->tree->setReturnValue('get_node', array('parent_id' => 110), array($node_id));
    
    $this->tree->expectNever('move_tree');
    
  	try
  	{
  	  $this->object->update();
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'new parent cant accept children');
  	}
  }

  function  test_update_ok_object_not_moved_identifier_changed_in_tree()
  {
    $this->object->set_id(1);
    $this->object->set_node_id($node_id = 10);
    $this->object->set_parent_node_id($parent_node_id = 100);
    $this->object->set_behaviour_id(1000);
    $this->object->set_identifier($identifier = 'test');
    
    $this->tree->expectOnce('get_node');
    $this->tree->setReturnValue('get_node', array('identifier' => 'test2', 'parent_id' => 100), array($node_id));
    
    $this->tree->expectNever('move_tree');
    
    $this->tree->expectOnce('update_node', array($node_id, array('identifier' => $identifier), true));
    
    $this->object->update();
  }

  function  test_update_ok_object_not_moved_identifier_not_changed_in_tree()
  {
    $this->object->set_id(1);
    $this->object->set_node_id($node_id = 10);
    $this->object->set_parent_node_id($parent_node_id = 100);
    $this->object->set_behaviour_id(1000);
    $this->object->set_identifier($identifier = 'test');
    
    $this->tree->expectOnce('get_node');
    $this->tree->setReturnValue('get_node', array('identifier' => 'test', 'parent_id' => 100), array($node_id));
    
    $this->tree->expectNever('move_tree');    
    $this->tree->expectNever('update_node');
    
    $this->object->update();
  }  

  function test_versioned_update()
  {
    $this->db->sql_insert('sys_site_object', array('id' => $object_id = 1,
                                                   'class_id' => 40,
                                                   'current_version' => $version = 3,
                                                   'created_date' => time(),
                                                   'creator_id' => $this->user->get_id()));

    $this->object->set_id(1);
    $this->object->set_node_id($node_id = 10);
    $this->object->set_parent_node_id($parent_node_id = 100);
    $this->object->set_behaviour_id(1000);
    $this->object->set_identifier($identifier = 'test2');
    $this->object->set('status', 31);
    $this->object->set('title', 'new title');

    $this->tree->setReturnValue('get_node', 
                                array('identifier' => 'test2', 'parent_id' => 100),
                                array($node_id));
    
    $this->object->update();
    
    $this->assertTrue($this->object->get_version(), 4);
    
	 	$this->_check_sys_site_object_record();
  }

  function test_cant_delete_no_id()
  {
  	try
  	{
  	  $this->object->can_delete();
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'object id not set');
  	}
  }

  function test_cant_delete_no_node_id()
  {
    $this->object->set_id(1);
  	try
  	{
  	  $this->object->can_delete();
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'node id not set');
  	}
  }

  function test_cant_delete()
  {
    $this->object->set_id(1);
    $this->object->set_node_id(2);
    
    $this->object->setReturnValue('_can_delete_site_object', false);
  	$this->assertFalse($this->object->can_delete());
  }

  function test_cant_delete_not_terminal_node()
  {
    $this->object->set_id(1);
    $this->object->set_node_id(2);
    
    $this->object->setReturnValue('_can_delete_site_object', true);
    
    $this->tree->setReturnValue('can_delete_node', false, array(2));
    
  	$this->assertFalse($this->object->can_delete());
  }
  
  function test_can_delete()
  {
    $this->object->set_id(1);
    $this->object->set_node_id(2);
    
    $this->object->setReturnValue('_can_delete_site_object', true);    
    $this->tree->setReturnValue('can_delete_node', true, array(2));
    
  	$this->assertTrue($this->object->can_delete());
  }
	      
  function test_delete()
  {
    $this->db->sql_insert('sys_site_object', array('id' => $object_id = 1));
    
    $this->object->set_id($object_id);
    $this->object->set_node_id($node_id = 2);
    
    $this->object->setReturnValue('_can_delete_site_object', true);    
    $this->tree->setReturnValue('can_delete_node', true, array($node_id));
    
    $this->tree->expectOnce('delete_node', array($node_id));
    
    $this->object->delete();
    
    $this->db->sql_select('sys_site_object', '*', 'id=' . $object_id);
    $this->assertTrue(!$record = $this->db->fetch_row());                              
  }

  function _check_sys_site_object_record()
	{
		$user = user :: instance();
		
  	$this->db->sql_select('sys_site_object', '*', 'id=' . $this->object->get_id());
  	$record = $this->db->fetch_row();
		$this->assertEqual($record['identifier'], $this->object->get_identifier());
  	$this->assertEqual($record['title'], $this->object->get_title());
  	$this->assertEqual($record['current_version'], $this->object->get_version());
  	$this->assertFalse(!$record['class_id']);
  	$this->assertEqual($record['creator_id'], $this->user->get_id());
  	$this->assertEqual($record['behaviour_id'], $this->object->get_behaviour_id());
  	$this->assertTrue((time() - $record['created_date']) <= 60);
  	$this->assertTrue((time() - $record['modified_date']) <= 60);
  }
}

?>