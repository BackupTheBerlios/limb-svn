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
require_once(LIMB_DIR . '/tests/cases/site_objects/__site_object_template.test.php');
require_once(LIMB_DIR . 'core/model/site_objects/content_object.class.php');

class content_object_template_test_adapter extends content_object
{
	var $content_object = null;
	
	function content_object_template_test_adapter($content_object)
	{
		$this->content_object = $content_object;
		
		parent :: content_object();
	}
	
	function _define_class_properties()
	{
		$props = $this->content_object->get_class_properties();
		
		if(!isset($props['db_table_name']))
			$props['db_table_name'] = get_class($this->content_object);
		
		return $props;
	}
	
	function get_db_table()
	{
		return $this->_get_db_table();
	}
}

SimpleTestOptions::ignore('test_content_object_template'); 

class test_content_object_template extends test_site_object_template 
{ 
  function test_content_object_template() 
  {
  	parent :: test_site_object_template();
  }
  
  function _clean_up()
  {
  	parent :: _clean_up();

  	$this->db->sql_delete('sys_object_version');
		
		$this->_clean_content_db_table_records();		
  }
  
  function _clean_content_db_table_records()
  {
		$content_object_adapter = new content_object_template_test_adapter($this->object);
		$db_table = $content_object_adapter->get_db_table();
  	$this->db->sql_delete($db_table->get_table_name());
  }
  
  function test_create()
  {
  	$this->_set_object_initial_attributes();
  	
  	parent :: test_create();

  	$this->_check_sys_object_version_record();

 		$this->_check_content_object_record();
  }
  
  function _set_object_initial_attributes()
  {
  }

	function _set_object_secondary_update_attributes()
	{
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
  	$this->_set_object_initial_attributes();
  	
  	$this->object->set_attribute('parent_id', $this->parent_node_id);
  	$this->object->set_identifier('test_node');

  	$id = $this->object->create();
  	$node_id = $this->object->get_node_id();

  	$this->object->set_identifier('new_article_test');
  	$this->object->set_title('New article test2');

  	$this->_set_object_secondary_update_attributes();
  	
  	$version = $this->object->get_version();
  	$result = $this->object->update($versioned);
  	
  	$this->assertTrue($result, 'update operation failed');

  	if ($versioned)
  		$this->assertEqual($version + 1, $this->object->get_version(), 'version index is the same as before versioned update');
  	else	
  		$this->assertEqual($version, $this->object->get_version(), 'version index schould be the same as before unversioned update');
  	
  	$this->_check_sys_site_object_tree_record();
  	
	 	$this->_check_sys_site_object_record();

 		$this->_check_sys_object_version_record();

 		$this->_check_content_object_record();
  }

  function test_delete()
  {
  	$this->_set_object_initial_attributes();
  	
  	parent :: test_delete();
  }

	function test_fetch()
	{
  	$this->_set_object_initial_attributes();
  	
  	parent :: test_fetch();
	}
		      
  function _check_sys_object_version_record()
	{
		$conditions['object_id'] = $this->object->get_id();
		$conditions['version'] = $this->object->get_version();
		
  	$this->db->sql_select('sys_object_version', '*', $conditions);
  	$record = $this->db->fetch_row();
  	
  	$this->assertEqual($record['object_id'], $this->object->get_id());
  	$this->assertEqual($record['version'], $this->object->get_version());
  	$this->assertEqual($record['creator_id'], user :: get_id());
	}	

  function _check_content_object_record()
	{
		$conditions['object_id'] = $this->object->get_id();
		$conditions['version'] = $this->object->get_version();

		$content_object_adapter = new content_object_template_test_adapter($this->object);
		$db_table = $content_object_adapter->get_db_table();
		$arr = $db_table->get_list($conditions, 'id');

  	$this->assertEqual(sizeof($arr), 1);
  	$record = current($arr);
		
		$attribs = $this->object->export_attributes();
		
		foreach($attribs as $name => $value)
			if (isset($record[$name]) && !in_array($name, array('id', 'object_id')))
				$this->assertEqual($record[$name], $value);
	}		
}

?>
