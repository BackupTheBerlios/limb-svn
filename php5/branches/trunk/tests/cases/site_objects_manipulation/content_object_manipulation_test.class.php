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
require_once(dirname(__FILE__) . '/site_object_manipulation_test.class.php');
require_once(LIMB_DIR . 'class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'class/core/site_objects/content_object.class.php');
require_once(LIMB_DIR . 'class/db_tables/content_object_db_table.class.php');

class news_object_manipulation_test extends content_object
{			
	function _define_class_properties()
	{
		return array(
			'ordr' => 1,
			'can_be_parent' => 1,
			'db_table_name' => 'test_news_object',
			'controller_class_name' => 'controller_test'
		);
	}
}

class test_news_object_db_table extends content_object_db_table
{		
  function _define_columns()
  {
  	return complex_array :: array_merge(
  	  parent :: _define_columns(),
  	  array(
        'annotation' => '',
        'content' => '',
        'news_date' => array('type' => 'date'),
      )
    );
  }
}

class content_object_manipulation_test extends site_object_manipulation_test 
{ 		 	
  function setUp()
  {
  	parent :: setUp();
  	
  	$this->object = new news_object_manipulation_test();  	
  }
    
  function _clean_up()
  {
  	parent :: _clean_up();
  	
  	$this->db->sql_delete('sys_object_version');
  	$this->db->sql_delete('test_news_object');
  }
  
  function test_create()
  {
  	$this->object->set('annotation', 'news annotation');
  	$this->object->set('content', 'news content');
  	$this->object->set('news_date', '2004-01-02 00:00:00');
  	
  	parent :: test_create();
  	
  	$this->_check_sys_object_version_record();
  }
	
  function test_versioned_update()
  {
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('node_test');
  	$this->object->set('annotation', 'news annotation');
  	$this->object->set('content', 'news content');
  	$this->object->set('news_date', '2004-01-02 00:00:00');
  	$this->object->create();
  	
  	$this->object->set_identifier('new_article_test');
  	$this->object->set_title('New article test2');
  	$this->object->set('annotation', 'news annotation2');
  	$this->object->set('content', 'news content2');
  	$this->object->set('news_date', '2004-02-02 00:00:00');
  	
  	$this->object->update();
  	  	
  	$this->_check_sys_site_object_tree_record();
  	
	 	$this->_check_sys_site_object_record();

 		$this->_check_sys_object_version_record();

 		$this->_check_content_object_record();
  }
  
  function test_fetch_version_failed()
  {  	
  	$this->assertIdentical(false, $this->object->fetch_version(10000));
  }
  
  function fetch_test_version()
  {
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('node_test');
  	$this->object->set('annotation', 'news annotation');
  	$this->object->set('content', 'news content');
  	$this->object->set('news_date', '2004-01-02 00:00:00');
  	$this->object->create();
  	
  	$old_attributes = $this->object->export();

  	$this->object->set_identifier('new_article_test');
  	$this->object->set_title('New article test2');
  	$this->object->set('annotation', 'news annotation2');
  	$this->object->set('content', 'news content2');
  	$this->object->set('news_date', '2004-02-02 00:00:00');
  	
  	$this->assertTrue($this->object->update(), 'update operation failed');
  	
  	$version_data = $this->object->fetch_version($this->object->get_version() - 1);
  	foreach($old_attributes as $attribute => $value)
  	{
  		$this->assertEqual($version_data[$attribute], $value, "version attribute '{$attribute}' value '{$version_data[$attribute]}' not equal to expected '{$value}'");
  	}
  }
  
  function recover_test_version()
  {
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('node_test');
  	$this->object->set('annotation', 'news annotation');
  	$this->object->set('content', 'news content');
  	$this->object->set('news_date', '2004-01-02 00:00:00');
  	$this->object->create();
  	
  	$old_attributes = $this->object->export();

  	$this->object->set_identifier('new_article_test');
  	$this->object->set_title('New article test2');
  	$this->object->set('annotation', 'news annotation2');
  	$this->object->set('content', 'news content2');
  	$this->object->set('news_date', '2004-02-02 00:00:00');
  	
  	$this->assertTrue($this->object->update(), 'update operation failed');
  	
  	$this->assertTrue($this->object->recover_version($this->object->get_version() - 1), 'recover operation failed');
		
		foreach($old_attributes as $attribute => $value)
		{
			if(($attribute == 'version') || ($attribute == 'record_id'))
				continue;
			
			$recovered_value = $this->object->get($attribute);
			$this->assertEqual($value, $recovered_value, "version attribute '{$attribute}' value '{$recovered_value}' not equal to expected '{$value}'");
		}
		
		$this->assertEqual($this->object->get_version(), 3);
  }

  function test_unversioned_update()
  {
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('node_test');
  	
  	$this->object->create();
		
  	$this->object->set_identifier('new_article_test');
  	$this->object->set_title('New article test');
  	
  	$this->object->update(false);

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

		$db_table = $this->object->get_db_table();
		$arr = $db_table->get_list($conditions, 'id');

  	$this->assertEqual(sizeof($arr), 1);
  	$record = current($arr);
		
		$attribs = $this->object->export();
		
		foreach($attribs as $name => $value)
			if (isset($record[$name]) && !in_array($name, array('id', 'object_id')))
				$this->assertEqual($record[$name], $value);
	}		
}

?>