<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: file_object.test.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/system/dir.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'core/model/site_object_factory.class.php');
require_once(LIMB_DIR . '/tests/cases/site_objects_testers/site_object_tester.class.php');

class file_object_tester extends site_object_tester
{  	
  function file_object_tester() 
  {
  	parent :: site_object_tester('file_object');
  }
    
  function _clean_up()
  {
  	parent :: _clean_up();
  	
		dir :: rm(MEDIA_DIR);
		$this->connection->sql_delete('file_object');
  	$this->connection->sql_delete('media');
  }
  
  function _set_file_create_attributes()
  {
		$this->object->set_attribute('tmp_file_path', LIMB_DIR . '/tests/images/1.jpg');
		$this->object->set_attribute('file_name','original_name.jpg');
		$this->object->set_attribute('mime_type', 'image/jpeg');
  }

  function _set_file_update_attributes()
  {
		$this->object->set_attribute('tmp_file_path', LIMB_DIR . '/tests/images/2.jpg');
		$this->object->set_attribute('file_name','original_name.jpg');
		$this->object->set_attribute('mime_type', 'image/jpeg');
  }

  function test_failed_create()
  {
  	$this->_set_file_create_attributes();
  	parent :: test_create();
  }
  
  function test_create()
  {		
		$this->_set_file_create_attributes();
  	parent :: test_create();
  }
  
  function test_versioned_update()
  {
		$this->_set_file_update_attributes();
  	parent :: test_versioned_update();
  }

  function test_unversioned_update()
  {
		$this->_set_file_update_attributes();
  	parent :: test_unversioned_update();
  }
  
  function test_delete()
  {
		$this->_set_file_create_attributes();
  	parent :: test_delete();
  }

  function test_create_file()
  {	
  	$this->_set_file_create_attributes();
  		
		$this->assertTrue($this->object->create_file(), __LINE__);
		
		$this->connection->sql_select('media');
		
		$arr = $this->connection->get_array();
		
		$media_id = $this->object->get_attribute('media_id');
		
		$this->assertTrue(is_array($arr), __LINE__);
		$this->assertEqual(sizeof($arr), 1, __LINE__);
		$this->assertEqual($arr[0]['id'], $media_id, __LINE__);
		$this->assertEqual($arr[0]['file_name'], $this->object->get_attribute('file_name'), __LINE__);
		$this->assertEqual($arr[0]['mime_type'], $this->object->get_attribute('mime_type'), __LINE__);
		$this->assertEqual($arr[0]['etag'], $this->object->get_attribute('etag'), __LINE__);
		
		$this->assertTrue(file_exists(MEDIA_DIR . $media_id  . '.media'), __LINE__);
		$this->assertEqual(filesize(MEDIA_DIR . $media_id  . '.media'), filesize($this->object->get_attribute('tmp_file_path')), __LINE__);
	}
	
	function test_failed_update_file()
	{
		$this->_set_file_update_attributes();
		
		$this->assertErrorPattern('/media id not set/');
		
		$this->assertFalse($this->object->update_file());
	}
	
	function test_update_file()
	{	
		$this->_set_file_create_attributes();
		
		$this->object->create_file();
		
		$this->connection->sql_select('media');
		
		$row = $this->connection->fetch_row();
			
		$this->_set_file_update_attributes();
		
		$this->object->set_attribute('media_id', $row['id']);
		
		$this->assertTrue($this->object->update_file(), __LINE__);
		
		$this->connection->sql_select('media');
		
		$arr = $this->connection->get_array();
		
		$media_id = $this->object->get_attribute('media_id');
		
		$this->assertTrue(is_array($arr), __LINE__);
		$this->assertEqual(sizeof($arr), 1, __LINE__);
		$this->assertEqual($arr[0]['id'], $media_id, __LINE__);
		$this->assertEqual($arr[0]['file_name'], $this->object->get_attribute('file_name'), __LINE__);
		$this->assertEqual($arr[0]['mime_type'], $this->object->get_attribute('mime_type'), __LINE__);
		$this->assertEqual($arr[0]['etag'], $this->object->get_attribute('etag'), __LINE__);
		
		$this->assertTrue(file_exists(MEDIA_DIR . $media_id  . '.media'), __LINE__);
		$this->assertEqual(filesize(MEDIA_DIR . $media_id  . '.media'), filesize($this->object->get_attribute('tmp_file_path')), __LINE__);
	}
	
	function test_fetch()
	{
		$this->_set_file_create_attributes();
		
		parent :: test_fetch();
	}
	
	function _compare_fetch_data($record)
	{
		parent :: _compare_fetch_data($record);
		
		$this->assertEqual($record['file_name'], $this->object->get_attribute('file_name'));
		$this->assertEqual($record['mime_type'], $this->object->get_attribute('mime_type'));
		$this->assertEqual($record['etag'], $this->object->get_attribute('etag'));
	}
}

?>