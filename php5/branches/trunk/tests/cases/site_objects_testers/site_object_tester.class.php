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
require_once(LIMB_DIR . 'class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'class/core/site_objects/site_object.class.php');
require_once(LIMB_DIR . 'class/core/site_objects/site_object_factory.class.php');
require_once(LIMB_DIR . 'class/core/actions/empty_action.class.php');

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

class site_object_tester extends LimbTestCase 
{ 
	var $db = null;
	var $class_name = '';
	var $object = null;
	
	var $parent_node_id = '';
	var $sub_node_id = '';
	
  function site_object_tester($class_name) 
  {
  	$this->db =& db_factory :: instance();
  	
  	$this->class_name = $class_name;

  	parent :: LimbTestCase();
  }
  
  function &_create_site_object()
  {
  	return site_object_factory :: create($this->class_name);
  }
    
  function setUp()
  {
  	$this->object = $this->_create_site_object();
  	
  	$this->_clean_up();
  	
  	debug_mock :: init($this);
  	
  	$user =& user :: instance();
  	$user->_set_id(10);
		
  	$tree =& tree :: instance();

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
 
   	$user =& user :: instance();
  	$user->logout();
  }
  
  function _clean_up()
  {
  	$this->db->sql_delete('sys_site_object');
  	$this->db->sql_delete('sys_site_object_tree');
  	$this->db->sql_delete('sys_class');
  }
  
  function test_class_properties()
  {
		$props = $this->object->get_class_properties();
		
		if(isset($props['abstract_class']) && $props['abstract_class'])
			return;
			
		$this->_do_test_class_properties($props);
  }
  
  function _do_test_class_properties($props)
  {
		if(isset($props['controller_class_name']))
		{
			$this->assertEqual(get_class($this->object->get_controller()), $props['controller_class_name']);
		}
  }
  
  function test_controller()
  {
  	$controller = $this->object->get_controller();
  	
		$definitions = $controller->get_actions_definitions();
		$controller_class = get_class($controller);
		
		$empty_action = new empty_action();
		
		foreach($definitions as $action => $data)
		{
			$this->assertTrue(isset($data['permissions_required']), 
				'controller: "' . $controller_class . 
				'" permissions_required property for action "' . $action . '"not set');
			
			$this->assertTrue(in_array($data['permissions_required'], array('r', 'w', 'rw')), 
				'controller: "' . $controller_class . 
				'" permissions_required property for action "' . $action . '"not valid');
			
			if (isset($data['template_path']))
			{
				$template = new template($data['template_path']);
				
				$this->_check_template($template);
			}
			
			if(isset($data['action_path']))
			{	
				debug_mock :: expect_never_write('write_error');
				
				$action_obj = action_factory :: create($data['action_path']);
				
				$this->assertNotIdentical($action_obj, $empty_action,
					'controller: "' . $controller_class . 
					'" action object for action "' . $action . '"not found');
					
				$this->_check_action($action_obj);
			}
			
			if(isset($data['action_name']))
			{
				$this->assertTrue(($data['action_name']), 
					'controller: "' . $controller_class . 
					'" action_name property for action "' . $action . '" is empty - check strings');
			}
		}
		
		$action = $controller->get_default_action();
		
		$this->assertTrue(isset($definitions[$action]), 
			'controller: "' . $controller_class . 
			'" default action "' . $action . '" doesnt exist');
  }
  
  function _check_action(&$action)
  {
		if(!is_subclass_of($action, 'form_create_site_object_action') &&
				!is_subclass_of($action, 'form_edit_site_object_action'))
		return;		

		$datamap = $action->get_datamap();

		$action->_init_validator(); //this is not a very good idea...
		$validator = $action->get_validator();
		
		$rules = $validator->get_rules();
		$site_object = $action->get_site_object();
		
		$attributes_definition = $site_object->get_attributes_definition();
		
		foreach($datamap as $src_field => $dst_field)
		{
			$this->assertTrue(isset($attributes_definition[$dst_field]),
			'no such field in site_object "' . get_class($site_object) . '" attributes definition "' . $dst_field. '" defined in action "' . get_class($action) . '"' );
		}
		
		foreach($rules as $rule)
		{
			if(!is_subclass_of($rule, 'single_field_rule'))
				continue;
				
			$field_name = $rule->get_field_name();
			
			$this->assertTrue(isset($datamap[$field_name]),
				'no such field in datamap(validator rule) "' . $field_name. '" in "' . get_class($action) . '"' );
		}
  }
  
  function _check_template(&$template)
  {
  }
  
  function test_failed_create()
  {
  	if(!$this->object->is_auto_identifier())
  	{
  		debug_mock :: expect_write_error('identifier is empty');
  	
  		$this->assertIdentical($this->object->create(), false, 'shouldn\'t be created, since identifier is not set');
  	}
  	
		$this->object->set_parent_node_id(1000000);

  	if(!$this->object->is_auto_identifier())
  	{
			debug_mock :: expect_write_error('identifier is empty');
		
  		$this->assertIdentical($this->object->create(), false, 'shouldn\'t be created, since identifier is not set');
  	}
  	
		$this->object->set_identifier('test');

  	if($this->object->is_auto_identifier())
			debug_mock :: expect_write_error(tree_driver :: TREE_ERROR_NODE_NOT_FOUND, array('id' => 1000000));
		else
			debug_mock :: expect_write_error('tree registering failed', array('parent_node_id' => 1000000));
		
  	$this->assertIdentical($this->object->create(), false, 'shouldn\'t be created, since parent node is not valid');
  }
	
  function test_create()
  {
  	debug_mock :: expect_never_write();
  	
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('test_site_object');
		
  	$id = $this->object->create();
  	
  	$this->assertNotIdentical($id, false, 'create operation failed');
  	
  	$this->assertEqual($id, $this->object->get_id(), 'new object id is not valid');

  	$this->_check_sys_site_object_tree_record();
  	
  	$this->_check_sys_site_object_record();
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

  	$this->object->set_identifier('new_test_identifier');
  	$this->object->set_title('New test title');
  	
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
  	
  	$sys_site_object_db_table =& db_table_factory :: create('sys_site_object');
  	$sys_site_object_tree_db_table =& db_table_factory :: create('sys_site_object_tree');
  	$sys_site_object_version_db_table =& db_table_factory :: create('sys_object_version');
  	
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
		$user =& user :: instance();
		   	
  	$this->db->sql_select('sys_site_object', '*', 'id=' . $this->object->get_id());
  	$record = $this->db->fetch_row();
		$this->assertEqual($record['identifier'], $this->object->get_identifier());
  	$this->assertEqual($record['title'], $this->object->get_title());
  	$this->assertEqual($record['current_version'], $this->object->get_version());
  	$this->assertFalse(!$record['class_id']);
  	$this->assertEqual($record['creator_id'], $user->get_id());
  	$this->assertTrue((time() - $record['created_date']) <= 60, 'create time is not valid');
  	$this->assertTrue((time() - $record['modified_date']) <= 60, 'modified time is not valid');
  }
  		
	function test_fetch()
	{
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('test_site_object');
		
  	$id = $this->object->create();

		$arr = $this->object->fetch();
		
		reset($arr);
		$this->assertNotEqual(sizeof($arr), 0, 'nothing fetched');
		$this->assertEqual(key($arr), $id, 'id doesnt match');
		
		$record = current($arr);
		
		$this->_compare_fetch_data($record);
	}
	
	function _compare_fetch_data($record)
	{
		$id = $this->object->get_id();
		
		$this->assertEqual($record['id'], $id, 'site object id doesnt match');
		$this->assertEqual($record['node_id'], $this->object->get_node_id(), 'node id doesnt match');
		$this->assertEqual($record['class_id'], $this->object->get_class_id(), 'class_id doesnt match');
		$this->assertEqual($record['class_name'], get_class($this->object), 'class name doesnt match');
		$this->assertEqual($record['identifier'], $this->object->get_identifier(), 'identifier doesnt match');
		$this->assertEqual($record['title'], $this->object->get_title(), 'title doesnt match');
		$this->assertEqual($record['parent_node_id'], $this->object->get_parent_node_id(), 'parent_node_id doesnt match');
		$this->assertEqual($record['version'], $this->object->get_version(), 'version doesnt match');
		
		$tree =& tree :: instance();
		
		$node = $tree->get_node($this->object->get_node_id());
		$this->assertEqual($record['level'], $node['level'], 'level doesnt match');
	}
}

?>