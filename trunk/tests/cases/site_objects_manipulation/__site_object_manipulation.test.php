<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: __site_object_manipulation.test.php 81 2004-03-26 13:51:05Z server $
*
***********************************************************************************/ 

require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_table_factory.class.php');
require_once(LIMB_DIR . 'core/model/site_objects/site_object.class.php');
require_once(LIMB_DIR . 'core/model/site_object_factory.class.php');

class site_object_manipulation_test extends site_object
{
	function site_object_manipulation_test()
	{
		parent :: site_object();
	}
		
	function _define_attributes_definition()
	{
		return complex_array :: array_merge(
				parent :: _define_attributes_definition(),
				array(
				'title' => '',
				'name' => array('type' => 'numeric'),
				'search' => array('search' => true),
				));		
	}
	
	function _define_class_properties()
	{
		return array(
			'ordr' => 1,
			'can_be_parent' => 1,
			'db_table_name' => 'site_object',
			'controller_class_name' => 'test_controller'
		);
	}
}

class test_site_object_manipulation extends UnitTestCase 
{ 
	var $connection = null;
	var $object = null;
	
	var $parent_node_id = '';
	var $sub_node_id = '';
		 	
  function test_site_object_manipulation() 
  {
  	$this->connection=& db_factory :: get_connection();

  	parent :: UnitTestCase();
  }

  function setUp()
  {
  	$this->_clean_up();
  	
  	$this->object = new site_object_manipulation_test();
  	
  	$user =& user :: instance();
  	$user->_set_id(10);
  	
  	$tree =& tree :: instance();

		$values['identifier'] = 'root';
		$root_node_id = $tree->create_root_node($values, false, true);

		$values['identifier'] = 'ru';
		$values['object_id'] = 1;
		$this->parent_node_id = $tree->create_sub_node($root_node_id, $values);

		$class_id = $this->object->get_class_id();
		
		$table = db_table_factory :: instance('sys_site_object');
		
		$table->insert(array('id' => 1, 'class_id' => $class_id, 'current_version' => 1));

		$values['identifier'] = 'document';
		$values['object_id'] = 10;
		$this->sub_node_id = $tree->create_sub_node($this->parent_node_id, $values);

		$class_id = $this->object->get_class_id();
		$table->insert(array('id' => 10, 'class_id' => $class_id, 'current_version' => 1));
  }
  
  function tearDown()
  { 
  	$this->_clean_up();
  	
  	$user =& user :: instance();
  	$user->logout();
  }
  
  function _clean_up()
  {
  	$table1 = db_table_factory :: instance('sys_site_object');
  	$table2 = db_table_factory :: instance('sys_site_object_tree');
  	$table3 = db_table_factory :: instance('sys_class');
  	
  	$table1->delete();
  	$table2->delete();
  	$table3->delete();
  }
  
  function test_failed_create()
  {
  	$this->assertIdentical($this->object->create(), false, 'create should fail here');
  	
  	$this->assertErrorPattern('/identifier is empty/');
  	
		$this->object->set_parent_node_id(10);
		
  	$this->assertIdentical($this->object->create(), false, 'create should fail here');
  	
  	$this->assertErrorPattern('/identifier is empty/');
  	
		$this->object->set_identifier('test');
		
  	$this->assertIdentical($this->object->create(), false, 'create should fail here');
  	
  	$this->assertErrorPattern('/tree registering failed/');
  }
	
  function test_create()
  {
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('test_node');
		
  	$id = $this->object->create();
  	
  	$this->assertNotIdentical($id, false, 'create operation failed');
  	
  	$this->assertEqual($id, $this->object->get_id());

  	$this->_check_sys_site_object_tree_record();
  	
  	$this->_check_sys_site_object_record();
		
  	$this->_check_sys_class_record();
  }
  
  function test_versioned_update()
  {
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('test_node');

  	$id = $this->object->create();
  	$node_id = $this->object->get_attribute('node_id');

  	$this->object->set_identifier('new_article_test');
  	$this->object->set_title('New article test');
  	
  	$result = $this->object->update();
  	$this->assertTrue($result, 'update operation failed');

  	$this->_check_sys_site_object_tree_record();
  	
	 	$this->_check_sys_site_object_record();
  }

  function test_unversioned_update()
  {
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('test_node');
  	
  	$this->object->create();
  	$this->object->get_node_id();
		
  	$this->object->set_identifier('new_article_test');
  	$this->object->set_title('New article test');
  	
  	$this->assertTrue($this->object->update(false), 'update operation failed');

  	$this->_check_sys_site_object_tree_record();
  	
	 	$this->_check_sys_site_object_record();
  }

  function test_can_not_delete()
  {
  	$data['id'] = 1;
  	$data['node_id'] = $this->parent_node_id;
  	$this->object->import_attributes($data);
  	
  	$result = $this->object->can_delete();
  	
  	$this->assertFalse($result);
  }
  
  function test_can_delete()
  {
  	$data['id'] = 10;
  	$data['node_id'] = $this->sub_node_id;
  	$this->object->import_attributes($data);
  	
  	$this->assertTrue($this->object->can_delete(), 'object can be deleted');
  }
	      
  function test_delete()
  {
  	$data['id'] = 10;
  	$data['node_id'] = $this->sub_node_id;
  	$this->object->import_attributes($data);
  	
  	$this->assertTrue($this->object->delete(), 'delete operation failed');
  	
  	$sys_site_object_db_table =& db_table_factory :: instance('sys_site_object');
  	$sys_site_object_tree_db_table =& db_table_factory :: instance('sys_site_object_tree');
  	$sys_site_object_version_db_table =& db_table_factory :: instance('sys_object_version');
  	
  	$arr = $sys_site_object_db_table->get_row_by_id($this->object->get_id());
  	$this->assertIdentical($arr, false);

  	$arr = $sys_site_object_tree_db_table->get_row_by_id($this->object->get_node_id());
  	$this->assertIdentical($arr, false);

  	$arr = $sys_site_object_version_db_table->get_list('object_id ='. $this->object->get_id());
  	$this->assertEqual(sizeof($arr), 0);
  }
	
  function _check_sys_site_object_tree_record()
	{
  	$this->connection->sql_select('sys_site_object_tree', '*', 'object_id=' . $this->object->get_id());
  	$record = $this->connection->fetch_row();
  	
  	$this->assertEqual($record['id'], $this->object->get_node_id());
  	$this->assertEqual($record['object_id'], $this->object->get_id());
  	$this->assertEqual($record['parent_id'], $this->parent_node_id);
  	$this->assertEqual($record['identifier'], $this->object->get_identifier());
	}
	
  function _check_sys_site_object_record()
	{
		$user =& user :: instance();
		
  	$this->connection->sql_select('sys_site_object', '*', 'id=' . $this->object->get_id());
  	$record = $this->connection->fetch_row();
		$this->assertEqual($record['identifier'], $this->object->get_identifier());
  	$this->assertEqual($record['title'], $this->object->get_title());
  	$this->assertEqual($record['current_version'], $this->object->get_version());
  	$this->assertFalse(!$record['class_id']);
  	$this->assertEqual($record['creator_id'], $user->get_id());
  	$this->assertTrue((time() - $record['created_date']) <= 60);
  	$this->assertTrue((time() - $record['modified_date']) <= 60);
  }
  	
  function _check_sys_class_record()
	{
  	$this->connection->sql_select('sys_class', '*', 'class_name="' . get_class($this->object) . '"');
  	$record = $this->connection->fetch_row();
  	$this->assertTrue(is_array($record));
	}
}

?>
