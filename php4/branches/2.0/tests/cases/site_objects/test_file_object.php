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
require_once(LIMB_DIR . 'core/lib/system/dir.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'core/model/site_object_factory.class.php');

class test_file_object extends UnitTestCase 
{  	
	var $db = null;

  function test_file_object() 
  {
  	parent :: UnitTestCase();

 		$this->db = db_factory :: instance();  	
  }
  
  function setUp()
  {
  	debug_mock :: init($this);
  	dir :: rm(MEDIA_DIR);
  	$this->db->sql_delete('media');    	
  }
  
  function tearDown()
  {
  	debug_mock :: tally();
		dir :: rm(MEDIA_DIR);
  	$this->db->sql_delete('media');
  }
  
  function test_create_file()
  {
		$obj =& site_object_factory :: create('file_object');
					
		$obj->set_attribute('tmp_file_path', PROJECT_DIR . 'images/1.jpg');
		$obj->set_attribute('file_name','original_name.jpg');
		$obj->set_attribute('mime_type', 'image/jpeg');
		
		$this->assertTrue($obj->create_file(), __LINE__);
		
		$this->db->sql_select('media');
		
		$arr = $this->db->get_array();
		
		$media_id = $obj->get_attribute('media_id');
		
		$this->assertTrue(is_array($arr), __LINE__);
		$this->assertEqual(sizeof($arr), 1, __LINE__);
		$this->assertEqual($arr[0]['id'], $media_id, __LINE__);
		$this->assertEqual($arr[0]['file_name'], $obj->get_attribute('file_name'), __LINE__);
		$this->assertEqual($arr[0]['mime_type'], $obj->get_attribute('mime_type'), __LINE__);
		
		$this->assertTrue(file_exists(MEDIA_DIR . $media_id  . '.media'), __LINE__);
		$this->assertEqual(filesize(MEDIA_DIR . $media_id  . '.media'), filesize($obj->get_attribute('tmp_file_path')), __LINE__);
	}
	
	function test_update_file()
	{	
		$this->test_create_file();
		$obj =& site_object_factory :: create('file_object');
		
		$this->db->sql_select('media');
		
		$row = $this->db->fetch_row();
	
		$obj->set_attribute('tmp_file_path', PROJECT_DIR . 'images/2.jpg');
		$obj->set_attribute('file_name','new_name.jpg');
		$obj->set_attribute('mime_type', 'image/jpeg');
		
		debug_mock :: expect_write_error('media id not set');
		
		$this->assertFalse($obj->update_file(), __LINE__);
		
		$obj->set_attribute('media_id', $row['id']);
		
		$this->assertTrue($obj->update_file(), __LINE__);
		
		$this->db->sql_select('media');
		
		$arr = $this->db->get_array();
		
		$media_id = $obj->get_attribute('media_id');
		
		$this->assertTrue(is_array($arr), __LINE__);
		$this->assertEqual(sizeof($arr), 1, __LINE__);
		$this->assertEqual($arr[0]['id'], $media_id, __LINE__);
		$this->assertEqual($arr[0]['file_name'], $obj->get_attribute('file_name'), __LINE__);
		$this->assertEqual($arr[0]['mime_type'], $obj->get_attribute('mime_type'), __LINE__);
		
		$this->assertTrue(file_exists(MEDIA_DIR . $media_id  . '.media'), __LINE__);
		$this->assertEqual(filesize(MEDIA_DIR . $media_id  . '.media'), filesize($obj->get_attribute('tmp_file_path')), __LINE__);

	}
}

?>