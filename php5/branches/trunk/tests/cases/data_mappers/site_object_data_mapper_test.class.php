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
require_once(LIMB_DIR . '/class/core/data_mappers/site_object_mapper.class.php');
require_once(LIMB_DIR . '/class/core/data_mappers/site_object_behaviour_mapper.class.php');
require_once(LIMB_DIR . '/class/core/finders/site_objects_raw_finder.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');
require_once(LIMB_DIR . '/class/core/behaviours/site_object_behaviour.class.php');
require_once(LIMB_DIR . '/class/core/base_limb_toolkit.class.php');
require_once(LIMB_DIR . '/class/core/tree/tree_decorator.class.php');
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');

Mock :: generatePartial('BaseLimbToolkit',
                      'SiteObjectToolkitMock', array());

class SiteObjectManipulationTestToolkit extends SiteObjectToolkitMock
{
  var $_mocked_methods = array('getTree', 'getUser', 'constant', 'createDataMapper');
  
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

  public function createDataMapper($path) 
  { 
    $args = func_get_args();
    return $this->_mock->_invoke('createDataMapper', $args); 
  }
  
  public function constant($name) 
  { 
    $args = func_get_args();
    return $this->_mock->_invoke('constant', $args); 
  } 
}

Mock :: generate('tree_decorator');
Mock :: generate('user');
Mock :: generate('site_object');
Mock :: generate('site_objects_raw_finder');
Mock :: generate('site_object_behaviour');
Mock :: generate('site_object_behaviour_mapper');

Mock :: generatePartial('site_object_mapper',
                      'site_object_mapper_test_version0',
                      array('insert', 
                            'update',
                            '_get_finder',
                            '_get_behaviour_mapper'));

Mock :: generatePartial('site_object_mapper',
                      'site_object_mapper_test_version1',
                      array('_insert_tree_node',
                            '_update_tree_node',
                            '_can_delete_site_object_record',
                            'get_class_id'));

Mock :: generatePartial('site_object_mapper',
                      'site_object_mapper_test_version2',
                      array('_can_add_node_to_parent', 
                            '_insert_site_object_record',
                            '_update_site_object_record'));


class site_object_mapper_test extends LimbTestCase 
{ 
	var $db;
	var $behaviour;
  var $behaviour_mapper;
  var $site_object;
  var $toolkit;
  var $tree;
  var $user;
  
  function setUp()
  {
    $this->toolkit = new SiteObjectManipulationTestToolkit($this);
    $this->tree = new Mocktree_decorator($this);
    $this->user = new Mockuser($this);
    $this->behaviour_mapper = new Mocksite_object_behaviour_mapper($this);
    $this->site_object = new Mocksite_object($this);
    $this->user->setReturnValue('get_id', 125);
    
    $this->toolkit->setReturnValue('getTree', $this->tree);
    $this->toolkit->setReturnValue('getUser', $this->user);
    $this->toolkit->setReturnValue('createDataMapper', 
                                   $this->behaviour_mapper, 
                                   array('site_object_behaviour_mapper'));
    
    $this->behaviour = new Mocksite_object_behaviour($this);
    
    Limb :: registerToolkit($this->toolkit);
    
  	$this->db = db_factory :: instance();
  	
  	$this->_clean_up();
  }
  
  function tearDown()
  { 
  	$this->_clean_up();
  	
    $this->toolkit->tally();
    $this->tree->tally();
    $this->site_object->tally();
    $this->behaviour->tally();
    $this->behaviour_mapper->tally();
    
    Limb :: popToolkit();
  }
  
  function _clean_up()
  {
  	$this->db->sql_delete('sys_site_object');
    $this->db->sql_delete('sys_site_object_tree');
    $this->db->sql_delete('sys_class');
  }

  function test_get_class_id()
  {
    $mapper = new site_object_mapper();
    $object = new site_object();
    
    // autogenerate class_id
		$id = $mapper->get_class_id($object);
		
		$this->db->sql_select('sys_class', '*', 'name="' . get_class($object) . '"');
		$arr = $this->db->fetch_row();
		
		$this->assertNotNull($id);
		
		$this->assertEqual($id, $arr['id']);

    // generate class_id only once
		$id = $mapper->get_class_id($object);
		$this->db->sql_select('sys_class', '*');
		$arr = $this->db->get_array();
		
		$this->assertEqual(sizeof($arr), 1);
	}
  
  function test_get_parent_locale_id_default()
  {
    $mapper = new site_object_mapper();
    
    $this->toolkit->setReturnValue('constant', 
                                   $locale_id  = 'ge', 
                                   array('DEFAULT_CONTENT_LOCALE_ID'));
    
    $this->assertEqual($mapper->get_parent_locale_id(10000), $locale_id);
  }
  
  function test_get_parent_locale_id()
  {
    $this->db->sql_insert('sys_site_object', array('locale_id' => $locale_id = 'ru',
                                                   'id' => 200));

    $this->db->sql_insert('sys_site_object_tree', array('object_id' => 200,
                                                        'id' => $parent_node_id = 300));
    
    $mapper = new site_object_mapper();
    
    $this->assertEqual($mapper->get_parent_locale_id($parent_node_id), $locale_id);
  }
  
  function test_find_by_id()
  {
    $finder = new Mocksite_objects_raw_finder($this);
    $result = array('id' => $id = 10,
                    'identifier' => $identifier = 'test',
                    'behaviour_id' => $behaviour_id = 100);
    
    $finder->expectOnce('find_by_id', array($id));
    $finder->setReturnValue('find_by_id', $result, array($id));
    
    $mapper = new site_object_mapper_test_version0($this);
    
    $mapper->setReturnValue('_get_finder', $finder);    
    $mapper->setReturnValue('_get_behaviour_mapper', $this->behaviour_mapper);
    
    $this->behaviour_mapper->expectOnce('find_by_id', array($behaviour_id));
    $this->behaviour_mapper->setReturnValue('find_by_id', $this->behaviour, array($behaviour_id));
    
    $site_object = $mapper->find_by_id($id);
    
    $this->assertEqual($site_object->get_id(), $id);
    $this->assertEqual($site_object->get_identifier(), $identifier);
    $this->assertTrue($site_object->get_behaviour() === $this->behaviour);

    $finder->tally();
    $mapper->tally();
  }
  
  function test_failed_insert_site_object_record_no_identifier()
  {
  	$mapper = new site_object_mapper_test_version1($this);
    $mapper->setReturnValue('get_class_id', 1000);
    
    $this->site_object->expectOnce('get_identifier');
    $this->site_object->setReturnValue('get_identifier', null);
    
  	try
  	{
  	  $mapper->insert($this->site_object);
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e){}
    
    $mapper->tally();
  }

  function test_failed_insert_site_object_record_no_behaviour_attached()
  {
  	$mapper = new site_object_mapper_test_version1($this);
    $mapper->setReturnValue('get_class_id', 1000);
    
    $this->site_object->setReturnValue('get_identifier', 'test');
    $this->site_object->expectOnce('get_behaviour');
    $this->site_object->setReturnValue('get_behaviour', null);
    
  	try
  	{
      $mapper->insert($this->site_object);
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'behaviour is not attached');
  	}
    
    $mapper->tally();
  }
  
  function test_insert_site_object_record_ok()
  {
  	$mapper = new site_object_mapper_test_version1($this);
    $mapper->setReturnValue('get_class_id', 1000);
    $mapper->expectOnce('_insert_tree_node');
    $mapper->setReturnValue('_insert_tree_node', $node_id = 120);
    
    $site_object = new site_object();
    $site_object->set_identifier('test');
    $site_object->set_title('test');
    $site_object->set_locale_id('fr');
    $site_object->attach_behaviour($this->behaviour);
    $this->behaviour->setReturnValue('get_id', 25);
    
    $this->behaviour_mapper->expectOnce('save', array(new IsAExpectation('Mocksite_object_behaviour')));
    
  	$id = $mapper->insert($site_object);
    
    $this->assertEqual($site_object->get_id(), $id);
    $this->assertEqual($site_object->get_node_id(), $node_id);
  	
  	$this->_check_sys_site_object_record($site_object);
    
    $mapper->tally();
  }
  
  function test_failed_insert_tree_node_parent_id_not_set()
  {	
  	$mapper = new site_object_mapper_test_version2($this);

    $mapper->expectOnce('_insert_site_object_record');
    $mapper->setReturnValue('_insert_site_object_record', $object_id = 120);
    
		$this->site_object->setReturnValue('get_id', $object_id);
    
  	try
  	{
  	  $mapper->insert($this->site_object);
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'tree parent node is empty');
  	}
    
    $mapper->tally();
  }

  function test_failed_insert_tree_node_cant_register_node()
  {
  	$mapper = new site_object_mapper_test_version2($this);

    $mapper->expectOnce('_insert_site_object_record');
    $mapper->setReturnValue('_insert_site_object_record', $object_id = 120);
    
		$this->site_object->setReturnValue('get_id', $object_id);
		$this->site_object->setReturnValue('get_parent_node_id', $parent_node_id = 10);
    
		$mapper->expectOnce('_can_add_node_to_parent', array($parent_node_id));
		$mapper->setReturnValue('_can_add_node_to_parent', false);
		 
  	try
  	{
  	  $mapper->insert($this->site_object);
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'tree registering failed');
  	  $this->assertEqual($e->getAdditionalParams(), array('parent_node_id' => 10));
  	}
  }

  function test_insert_tree_node_ok()
  {
  	$mapper = new site_object_mapper_test_version2($this);

    $mapper->expectOnce('_insert_site_object_record');
    $mapper->setReturnValue('_insert_site_object_record', $object_id = 120);
    
		$this->site_object->setReturnValue('get_id', $object_id);
		$this->site_object->setReturnValue('get_parent_node_id', $parent_node_id = 10);

		$mapper->setReturnValue('_can_add_node_to_parent', true);
		$this->tree->expectOnce('create_sub_node');
    $this->tree->setReturnValue('create_sub_node', $node_id = 200);

 		$this->site_object->expectOnce('set_id', array($object_id));
 		$this->site_object->expectOnce('set_node_id', array($node_id));

  	$id = $mapper->insert($this->site_object);
  
    $mapper->tally();  	
  }

  function  test_update_site_object_record_failed_no_id()
  {
  	$mapper = new site_object_mapper_test_version1($this);
    $this->site_object->expectOnce('get_id');
    
  	try
  	{
  	  $mapper->update($this->site_object);
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'object id not set');
  	}
    
    $mapper->tally();
  }
  
  function  test_update_site_object_record_failed_no_behaviour_id()
  {
  	$mapper = new site_object_mapper_test_version1($this);
    $this->site_object->setReturnValue('get_id', 125);
    $this->site_object->setReturnValue('get_identifier', 'test');
    $this->site_object->expectOnce('get_behaviour');
    $this->site_object->setReturnValue('get_behaviour', null);
    
  	try
  	{
  	  $mapper->update($this->site_object);
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'behaviour id not attached');
  	}
    
    $mapper->tally();
  }

  function  test_update_site_object_record_failed_no_identifier()
  {
  	$mapper = new site_object_mapper_test_version1($this);
    $this->site_object->setReturnValue('get_id', 125);
    $this->site_object->setReturnValue('get_identifier', null);
    
  	try
  	{
  	  $mapper->update($this->site_object);
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'identifier is empty');
  	}
    
    $mapper->tally();
  }
  
  function test_update_site_object_record_ok()
  {
    $this->db->sql_insert('sys_site_object',
                          array('id' => $object_id = 100,
                                'title' => 'old title',
                                'identifier' => 'old identifier',
                                'class_id' => 234));

  	$mapper = new site_object_mapper_test_version1($this);
    
    $site_object = new site_object();
    $site_object->set_id($object_id);
    $site_object->set_identifier('test');
    $site_object->set_title('test');
    $site_object->set_locale_id('fr');
    $site_object->attach_behaviour($this->behaviour);
    $this->behaviour->setReturnValue('get_id', 25);
    
    $this->behaviour_mapper->expectOnce('save', array(new IsAExpectation('Mocksite_object_behaviour')));
    
  	$mapper->update($site_object);

  	$this->_check_sys_site_object_record($site_object);
    
    $mapper->tally();
  }
  
  function  test_update_tree_node_failed_no_node_id()
  {
  	$mapper = new site_object_mapper_test_version2($this);
    
  	try
  	{
  	  $mapper->update($this->site_object);
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'node id not set');
  	}
  }

  function  test_update_tree_node_failed_no_parent_node_id()
  {
  	$mapper = new site_object_mapper_test_version2($this);

    $this->site_object->setReturnValue('get_node_id', 10);

  	try
  	{
  	  $mapper->update($this->site_object);
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'parent node id not set');
  	}
  }

  function  test_update_tree_node_failed_to_move()
  {
    $this->site_object->setReturnValue('get_node_id', $node_id = 100);
    $this->site_object->setReturnValue('get_parent_node_id', $parent_node_id = 10);
    
    $mapper = new site_object_mapper_test_version2($this);
    $mapper->setReturnValue('_can_add_node_to_parent', true, array($parent_node_id));
    
    $this->tree->expectOnce('get_node');
    $this->tree->setReturnValue('get_node', array('parent_id' => 110), array($node_id));
    
    $this->tree->expectOnce('move_tree');
    $this->tree->setReturnValue('move_tree', false, array($node_id, $parent_node_id));
    
  	try
  	{
  	  $mapper->update($this->site_object);
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'could not move node');
  	}
    
    $mapper->tally();
  }
  
  function  test_update_tree_node_failed_new_parent_cant_accept_children()
  {
    $this->site_object->setReturnValue('get_node_id', $node_id = 100);
    $this->site_object->setReturnValue('get_parent_node_id', $parent_node_id = 10);
    
    $mapper = new site_object_mapper_test_version2($this);
    $mapper->setReturnValue('_can_add_node_to_parent', false, array($parent_node_id));
    
    $this->tree->expectOnce('get_node');
    $this->tree->setReturnValue('get_node', array('parent_id' => 110), array($node_id));
    
    $this->tree->expectNever('move_tree');
    
  	try
  	{
  	  $mapper->update($this->site_object);
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'new parent cant accept children');
  	}
  }
  
  function  test_update_ok_object_not_moved_identifier_changed_in_tree()
  {
    $this->site_object->setReturnValue('get_node_id', $node_id = 100);
    $this->site_object->setReturnValue('get_parent_node_id', $parent_node_id = 10);
    $this->site_object->setReturnValue('get_identifier', $identifier = 'test');

    $mapper = new site_object_mapper_test_version2($this);
    $mapper->setReturnValue('_can_add_node_to_parent', true, array($parent_node_id));
    
    $this->tree->expectOnce('get_node');
    $this->tree->setReturnValue('get_node', 
                                array('identifier' => 'test2', 'parent_id' => $parent_node_id), 
                                array($node_id));
    
    $this->tree->expectNever('move_tree');
    
    $this->tree->expectOnce('update_node', array($node_id, 
                                                 array('identifier' => $identifier), 
                                                 true));
    
    $mapper->update($this->site_object);
  }

  function  test_update_ok_object_not_moved_identifier_not_changed_in_tree()
  {
    $this->site_object->setReturnValue('get_node_id', $node_id = 100);
    $this->site_object->setReturnValue('get_parent_node_id', $parent_node_id = 10);
    $this->site_object->setReturnValue('get_identifier', $identifier = 'test');

    $mapper = new site_object_mapper_test_version2($this);
    $mapper->setReturnValue('_can_add_node_to_parent', true, array($parent_node_id));
    
    $this->tree->expectOnce('get_node');
    $this->tree->setReturnValue('get_node', 
                                array('identifier' => $identifier, 'parent_id' => $parent_node_id), 
                                array($node_id));
    
    $this->tree->expectNever('move_tree');
    $this->tree->expectNever('update_node');
    
    $mapper->update($this->site_object);
  }  

  function test_cant_delete_no_id()
  {
    $mapper = new site_object_mapper();
    
  	try
  	{
  	  $mapper->can_delete($this->site_object);
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'object id not set');
  	}
  }

  function test_cant_delete_no_node_id()
  {
    $mapper = new site_object_mapper();
    $this->site_object->setReturnValue('get_id', 10);
    
  	try
  	{
  	  $mapper->can_delete($this->site_object);
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'node id not set');
  	}
  }

  function test_cant_delete()
  {
    $this->site_object->setReturnValue('get_id', 10);
    $this->site_object->setReturnValue('get_node_id', 100);
    
    $mapper = new site_object_mapper_test_version1($this);
    $mapper->setReturnValue('_can_delete_site_object_record', false);
  	$this->assertFalse($mapper->can_delete($this->site_object));
  }
  
  function test_cant_delete_not_terminal_node()
  {
    $this->site_object->setReturnValue('get_id', 10);
    $this->site_object->setReturnValue('get_node_id', $node_id = 100);
    
    $mapper = new site_object_mapper_test_version1($this);
    $mapper->setReturnValue('_can_delete_site_object_record', true);
    
    $this->tree->expectOnce('can_delete_node', array($node_id));
    $this->tree->setReturnValue('can_delete_node', false, array($node_id));
    
  	$this->assertFalse($mapper->can_delete($this->site_object));
  }
  
  function test_can_delete()
  {
    $this->site_object->setReturnValue('get_id', 10);
    $this->site_object->setReturnValue('get_node_id', $node_id = 100);
    
    $mapper = new site_object_mapper_test_version1($this);
    $mapper->setReturnValue('_can_delete_site_object_record', true);
    $this->tree->setReturnValue('can_delete_node', true, array($node_id));
    
  	$this->assertTrue($mapper->can_delete($this->site_object));
  }
	      
  function test_delete()
  {
    $this->db->sql_insert('sys_site_object', array('id' => $object_id = 1));
    
    $this->site_object->setReturnValue('get_id', $object_id);
    $this->site_object->setReturnValue('get_node_id', $node_id = 100);
    
    $mapper = new site_object_mapper_test_version1($this);
    $mapper->setReturnValue('_can_delete_site_object_record', true);
    $this->tree->setReturnValue('can_delete_node', true, array($node_id));
    
    $this->tree->expectOnce('delete_node', array($node_id));
    
    $mapper->delete($this->site_object);
    
    $this->db->sql_select('sys_site_object', '*', 'id=' . $object_id);
    $this->assertTrue(!$record = $this->db->fetch_row());                              
  }

  function _check_sys_site_object_record($site_object)
	{
  	$this->db->sql_select('sys_site_object', '*', 'id=' . $site_object->get_id());
    
  	$record = $this->db->fetch_row();
    
    $this->assertNotNull($site_object->get_identifier());
		$this->assertEqual($record['identifier'], $site_object->get_identifier());
    
    $this->assertNotNull($site_object->get_title());
  	$this->assertEqual($record['title'], $site_object->get_title());
    
    $this->assertNotNull($site_object->get_version());
  	$this->assertEqual($record['current_version'], $site_object->get_version());
    
    $this->assertNotNull($site_object->get_locale_id());
  	$this->assertEqual($record['locale_id'], $site_object->get_locale_id());
    
  	$this->assertFalse(!$record['class_id']);//???
    
    $this->assertNotNull($site_object->get_creator_id());
  	$this->assertEqual($record['creator_id'], $site_object->get_creator_id());
    
    $this->assertNotNull($site_object->get_behaviour()->get_id());
  	$this->assertEqual($record['behaviour_id'], $site_object->get_behaviour()->get_id());
    
    $this->assertNotNull($site_object->get_created_date());
  	$this->assertEqual($record['created_date'], $site_object->get_created_date());
    
    $this->assertNotNull($site_object->get_modified_date());
    $this->assertEqual($record['modified_date'], $site_object->get_modified_date());
  }
}

?>