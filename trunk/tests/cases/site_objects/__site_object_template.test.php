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

require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'core/model/site_objects/site_object.class.php');
require_once(LIMB_DIR . 'core/model/site_object_factory.class.php');

class mock_root_object extends site_object
{
	function mock_root_object()
	{
		parent :: site_object();
	}
	
	function _define_class_properties()
	{
		return array(
			'can_be_parent' => 1,
		);
	}
}

SimpleTestOptions::ignore('test_site_object_template'); 

class test_site_object_template extends UnitTestCase 
{ 
	var $db = null;
	var $object = null;
	
	var $parent_node_id = '';
	var $sub_node_id = '';
	
  function test_site_object_template() 
  {
  	$this->db =& db_factory :: instance();

  	parent :: UnitTestCase();
  }
  
  function & _create_site_object()
  {
  	return null;
  }

  function setUp()
  {
  	$this->object =& $this->_create_site_object();

  	$this->_clean_up();
  	
  	debug_mock :: init($this);
  	
		$_SESSION[user :: get_session_identifier()]['id'] = 10;
		
  	$tree =& limb_tree :: instance();

		$values['identifier'] = 'root';
		$values['object_id'] = 1;
		$this->parent_node_id = $tree->create_root_node($values, false, true);
		
		$this->db->sql_insert('sys_class', array('id' => 1, 'class_name' => 'mock_root_object'));
		$this->db->sql_insert('sys_site_object', array('id' => 1, 'class_id' => 1, 'current_version' => 1, 'identifier' => 'root'));
  }
  
  function tearDown()
  { 
  	$this->_clean_up();
  	
  	debug_mock :: tally();
  	
  	user :: logout();
  }
  
  function _clean_up()
  {
  	$this->db->sql_delete('sys_site_object');
  	$this->db->sql_delete('sys_site_object_tree');
  	$this->db->sql_delete('sys_class');
  }
  
  function test_failed_create()
  {
  	debug_mock :: expect_write_error('identifier is empty');
  	
  	$this->assertIdentical($this->object->create(), false);
  	
		$this->object->set_parent_node_id(1000000);
		
		debug_mock :: expect_write_error('identifier is empty');
		
  	$this->assertIdentical($this->object->create(), false);
  	
		$this->object->set_identifier('test');
		
		debug_mock :: expect_write_error('tree registering failed', array('parent_node_id' => 1000000));
		
  	$this->assertIdentical($this->object->create(), false);
  }
	
  function test_create()
  {
  	debug_mock :: expect_never_write();
  	
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('test_site_object');
		
  	$id = $this->object->create();
  	
  	$this->assertNotIdentical($id, false, 'create operation failed');
  	
  	$this->assertEqual($id, $this->object->get_id());

  	$this->_check_sys_site_object_tree_record();
  	
  	$this->_check_sys_site_object_record();
		
  	$this->_check_sys_class_record();
  }

	function test_versioned_update()
	{
		$this->_test_update(true);
	}

	function test_unversioned_update()
	{
		$this->_test_update(false);
	}
	
  function _test_update($versioned = false)
  {
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('test_site_object');

  	$id = $this->object->create();
  	$node_id = $this->object->get_node_id();

  	$this->object->set_identifier('new_article_test');
  	$this->object->set_title('New article test');
  	
  	$version = $this->object->get_version();
  	$result = $this->object->update($versioned);
  	$this->assertTrue($result, 'update operation failed');

  	if ($versioned)
  		$this->assertEqual($version + 1, $this->object->get_version(), 'version index is the same as before versioned update');
  	else	
  		$this->assertEqual($version, $this->object->get_version(), 'version index schould be the same as before unversioned update');

  	$this->_check_sys_site_object_tree_record();
  	
	 	$this->_check_sys_site_object_record();
  }

  function test_delete()
  {
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('test_site_object');
		
  	$id = $this->object->create();

  	$result = $this->object->delete();
  	
  	$this->assertTrue($result);
  	
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
  	$this->db->sql_select('sys_site_object_tree', '*', 'object_id=' . $this->object->get_id());
  	$record = $this->db->fetch_row();
  	
  	$this->assertEqual($record['id'], $this->object->get_node_id());
  	$this->assertEqual($record['object_id'], $this->object->get_id());
  	$this->assertEqual($record['parent_id'], $this->parent_node_id);
  	$this->assertEqual($record['identifier'], $this->object->get_identifier());
	}
	
  function _check_sys_site_object_record()
	{
  	$this->db->sql_select('sys_site_object', '*', 'id=' . $this->object->get_id());
  	$record = $this->db->fetch_row();
		$this->assertEqual($record['identifier'], $this->object->get_identifier());
  	$this->assertEqual($record['title'], $this->object->get_title());
  	$this->assertEqual($record['current_version'], $this->object->get_version());
  	$this->assertFalse(!$record['class_id']);
  	$this->assertEqual($record['creator_id'], user :: get_id());
  	$this->assertTrue((time() - $record['created_date']) <= 60);
  	$this->assertTrue((time() - $record['modified_date']) <= 60);
  }
  	
  function _check_sys_class_record()
	{
  	$this->db->sql_select('sys_class', '*', 'class_name="' . get_class($this->object) . '"');
  	$record = $this->db->fetch_row();
  	$this->assertTrue(is_array($record));
	}
	
	function test_fetch()
	{
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('test_site_object');
		
  	$id = $this->object->create();

		$arr = $this->object->fetch();
		
		reset($arr);
		$this->assertNotEqual(sizeof($arr), 0);
		$this->assertEqual(key($arr), $id, __FILE__ . ' : ' . __LINE__ . ': id doesnt match');
		
		$record = current($arr);
		
		$this->_compare_fetch_data($record);
	}
	
	function _compare_fetch_data($record)
	{
		$id = $this->object->get_id();
		
		$this->assertEqual($record['id'], $id, 'site object id doesnt match');
		$this->assertEqual($record['node_id'], $this->object->get_node_id(), __FILE__ . ' : ' . __LINE__ . ': node id doesnt match');
		$this->assertEqual($record['class_id'], $this->object->get_class_id(), __FILE__ . ' : ' . __LINE__ . ': class_id doesnt match');
		$this->assertEqual($record['class_name'], get_class($this->object), __FILE__ . ' : ' . __LINE__ . ': class name doesnt match');
		$this->assertEqual($record['identifier'], $this->object->get_identifier(), __FILE__ . ' : ' . __LINE__ . ': identifier doesnt match');
		$this->assertEqual($record['title'], $this->object->get_title(), __FILE__ . ' : ' . __LINE__ . ': title doesnt match');
		$this->assertEqual($record['parent_node_id'], $this->object->get_parent_node_id(), __FILE__ . ' : ' . __LINE__ . ': parent_node_id doesnt match');
		$this->assertEqual($record['version'], $this->object->get_version(), __FILE__ . ' : ' . __LINE__ . ': version doesnt match');
		
		$tree =& limb_tree :: instance();
		
		$node = $tree->get_node($this->object->get_node_id());
		$this->assertEqual($record['l'], $node['l'], __FILE__ . ' : ' . __LINE__ . ': l doesnt match');
		$this->assertEqual($record['r'], $node['r'], __FILE__ . ' : ' . __LINE__ . ': r doesnt match');
		$this->assertEqual($record['level'], $node['level'], __FILE__ . ' : ' . __LINE__ . ': level doesnt match');
	}
}

?>
