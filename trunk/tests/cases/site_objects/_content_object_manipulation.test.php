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
require_once(LIMB_DIR . 'core/model/site_objects/content_object.class.php');
require_once(LIMB_DIR . 'core/db_tables/content_object_db_table.class.php');

class news_object_manipulation_test extends content_object
{
	function news_object_manipulation_test()
	{
		parent :: content_object();
	}
			
	function _define_class_properties()
	{
		return array(
			'ordr' => 1,
			'can_be_parent' => 1,
			'db_table_name' => 'news_object_test',
			'controller_class_name' => 'test_controller'
		);
	}
}

class news_object_test_db_table extends content_object_db_table
{
	function news_object_test_db_table()
	{
		parent :: content_object_db_table();
	}
	
	function _define_db_table_name()
	{
		return 'news_object';
	}
	
  function _define_columns()
  {
  	return array(
      'annotation' => '',
      'content' => '',
      'news_date' => array('type' => 'date'),
    );
  }
}

class test_content_object_manipulation extends test_site_object_manipulation 
{ 		 	
  function test_site_object_manipulation() 
  {
  	parent :: test_content_object_manipulation();
  }

  function setUp()
  {
  	parent :: setUp();
  	
  	$this->object = new news_object_manipulation_test();  	
  }
    
  function _clean_up()
  {
  	parent :: _clean_up();
  	
  	$this->db->sql_delete('sys_object_version');
  	$this->db->sql_delete('news_object');
  }
  
  function test_failed_create()
  {
		parent :: test_failed_create();
  }
	
  function test_create()
  {
  	$this->object->set_attribute('annotation', 'news annotation');
  	$this->object->set_attribute('content', 'news content');
  	$this->object->set_attribute('news date', '2004-01-02');
  	
  	parent :: test_create();
  	
  	$this->_check_sys_object_version_record();
  }
	
  function test_versioned_update()
  {
  	$this->object->set_attribute('annotation', 'news annotation');
  	$this->object->set_attribute('content', 'news content');
  	$this->object->set_attribute('news date', '2004-01-02');

  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('test_node');

  	$id = $this->object->create();
  	$node_id = $this->object->get_node_id();

  	$this->object->set_identifier('new_article_test');
  	$this->object->set_title('New article test2');
  	$this->object->set_attribute('annotation', 'news annotation2');
  	$this->object->set_attribute('content', 'news content2');
  	$this->object->set_attribute('news date', '2004-02-02');
  	
  	$result = $this->object->update();
  	$this->assertTrue($result, 'update operation failed');

  	$this->_check_sys_site_object_tree_record();
  	
	 	$this->_check_sys_site_object_record();

 		$this->_check_sys_object_version_record();

 		$this->_check_content_object_record();
  }

  function test_unversioned_update()
  {
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('test_node');
  	
  	$id = $this->object->create();
  	$node_id = $this->object->get_node_id();
		
  	$this->object->set_identifier('new_article_test');
  	$this->object->set_title('New article test');
  	
  	$result = $this->object->update(false);
  	
  	$this->assertTrue($result, 'update operation failed');

  	$this->_check_sys_site_object_tree_record();
  	
	 	$this->_check_sys_site_object_record();

 		$this->_check_sys_object_version_record();
  }
	      
  function test_delete()
  {
  	parent :: test_delete();
  }
	  
  function _check_sys_object_version_record()
	{
		$conditions['object_id'] = $this->object->get_id();
		$conditions['version'] = $this->object->get_version();
	
  	$this->db->sql_select('sys_object_version', '*', $conditions);
  	$record = $this->db->fetch_row();
  	
  	$user =& user :: instance(); 
  	
  	$this->assertEqual($record['object_id'], $this->object->get_id());
  	$this->assertEqual($record['version'], $this->object->get_version());
  	$this->assertEqual($record['creator_id'], $user->get_id());
	}	

  function _check_content_object_record()
	{
		$conditions['object_id'] = $this->object->get_id();
		$conditions['version'] = $this->object->get_version();

		$db_table = $this->object->_get_db_table();
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
