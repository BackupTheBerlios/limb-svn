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
require_once(LIMB_DIR . 'core/model/site_objects/image_object.class.php');

Mock::generatePartial
(
  'image_object',
  'image_object_test_version',
  array('_check_result')
); 
  
class test_image_object extends UnitTestCase 
{  	
	var $db = null;
	
	var $obj = null;

  function test_image_object() 
  {
  	parent :: UnitTestCase();
		
 		$this->db = db_factory :: instance();  	
  }
  
	function setUp()
	{
		$this->_clean_up();
		
		$this->obj =& new image_object_test_version($this);
		$this->obj->image_object();
	} 
	
	function tearDown()
	{
		$this->_clean_up();
		
		$this->obj->tally();
		unset($this->obj);
	} 
      
  function _clean_up()
  {
		dir :: rm(MEDIA_DIR);
  	$this->db->sql_delete('image_variation');
  	$this->db->sql_delete('media');
  }
      
  function test_create_variations()
  {
		$files = array(
			'tmp_name' => array(
				'original' => PROJECT_DIR . 'images/1.jpg',
				'thumbnail' => PROJECT_DIR . 'images/2.jpg',
				'icon' => '',
			),
			'name' => array(
				'original' => 'original_name.jpg',
				'thumbnail' => 'thumbnail_name.jpg',
				'icon' => '',
			),
			'type' => array(
				'original' => 'image/jpeg',
				'thumbnail' => 'image/jpeg',
				'icon' => '',
			),
		);
					
		$this->obj->set_attribute('files_data', $files);
		$this->obj->set_attribute('id', 100);

		$this->obj->set_attribute('original_action', 'upload');

		$this->obj->set_attribute('thumbnail_action', 'upload');
		$this->obj->set_attribute('upload_thumbnail_max_size', 100);
		
		$this->obj->set_attribute('icon_action', 'generate');
		$this->obj->set_attribute('icon_base_variation', 'thumbnail');
		$this->obj->set_attribute('generate_icon_max_size', 50);

		$this->obj->expectOnce('_check_result');
		
		$affected_variations['original']['saved'] = true;
		$affected_variations['thumbnail']['saved'] = true;
		$affected_variations['thumbnail']['resized'] = true;
		$affected_variations['icon']['saved'] = true;
		$affected_variations['icon']['resized'] = true;
		
		$this->obj->expectArguments('_check_result', 
			array($affected_variations));
		
		$this->obj->create_variations();
								
		$this->db->sql_select('image_variation');
		
		$arr = $this->db->get_array();
		
		$this->assertTrue(is_array($arr), __LINE__);
		$this->assertEqual(sizeof($arr), 3, __LINE__);
		$this->assertEqual($arr[0]['image_id'], 100, __LINE__);
		
		$sql = "SELECT m.id as media_id, iv.variation
						FROM image_variation iv, media m
						WHERE 
							iv.media_id=m.id AND
							iv.image_id=100";

		$this->db->sql_exec($sql);
		
		$res = $this->db->get_array('variation');
		
		$this->assertTrue(file_exists(MEDIA_DIR . $res['original']['media_id'] . '.media'), __LINE__);
		$this->assertEqual(filesize(MEDIA_DIR . $res['original']['media_id'] . '.media'), filesize($files['tmp_name']['original']), __LINE__);
		$info = getimagesize(MEDIA_DIR . $res['original']['media_id'] . '.media');
		$info2 = getimagesize($files['tmp_name']['original']);
		$this->assertEqual(max($info[0], $info[1]), max($info2[0], $info2[1]));

		$this->assertTrue(file_exists(MEDIA_DIR . $res['thumbnail']['media_id'] . '.media'), __LINE__);
		$info = getimagesize(MEDIA_DIR . $res['thumbnail']['media_id'] . '.media');
		$this->assertEqual(max($info[0], $info[1]), 100, __LINE__);

		$this->assertTrue(file_exists(MEDIA_DIR . $res['icon']['media_id'] . '.media'), __LINE__);
		$info = getimagesize(MEDIA_DIR . $res['icon']['media_id'] . '.media');
		$this->assertEqual(max($info[0], $info[1]), 50, __LINE__);
	}
	
  function test_update_variations()
  {
  	$this->test_create_variations();
  	
  	$this->obj->clearHistory();
  	
		$files = array(
			'tmp_name' => array(
				'original' => PROJECT_DIR . 'images/1.jpg',
				'thumbnail' => '',
				'icon' => '',
			),
			'name' => array(
				'original' => 'original_name.jpg',
				'thumbnail' => '',
				'icon' => '',
			),
			'type' => array(
				'original' => 'image/jpeg',
				'thumbnail' => '',
				'icon' => '',
			),
		);
		
		$this->obj->set_attribute('files_data', $files);
		$this->obj->set_attribute('id', 100);

		$this->obj->set_attribute('original_action', 'upload');

		$this->obj->set_attribute('thumbnail_action', 'generate');
		$this->obj->set_attribute('thumbnail_base_variation', 'thumbnail');
		$this->obj->set_attribute('generate_thumbnail_max_size', 90);
		
		$this->obj->set_attribute('icon_action', 'generate');
		$this->obj->set_attribute('icon_base_variation', 'original');
		$this->obj->set_attribute('generate_icon_max_size', 30);
		
		$this->obj->expectOnce('_check_result');
		
		$affected_variations['original']['saved'] = true;
		$affected_variations['thumbnail']['saved'] = true;
		$affected_variations['thumbnail']['resized'] = true;
		$affected_variations['icon']['saved'] = true;
		$affected_variations['icon']['resized'] = true;
		
		$this->obj->expectArguments('_check_result', 
			array($affected_variations));

		$this->obj->update_variations();
				
		$this->db->sql_select('image_variation');
		
		$arr = $this->db->get_array();
		
		$this->assertTrue(is_array($arr), __LINE__);
		$this->assertEqual(sizeof($arr), 3, __LINE__);
		$this->assertEqual($arr[0]['image_id'], 100, __LINE__);
		
		$sql = "SELECT m.id as media_id, iv.variation
						FROM image_variation iv, media m
						WHERE 
							iv.media_id=m.id AND
							iv.image_id=100";

		$this->db->sql_exec($sql);
		
		$res = $this->db->get_array('variation');
		
		$this->assertTrue(file_exists(MEDIA_DIR . $res['original']['media_id'] . '.media'), __LINE__);
		$this->assertEqual(filesize(MEDIA_DIR . $res['original']['media_id'] . '.media'), filesize($files['tmp_name']['original']), __LINE__);
		$info = getimagesize(MEDIA_DIR . $res['original']['media_id'] . '.media');
		$info2 = getimagesize($files['tmp_name']['original']);
		$this->assertEqual(max($info[0], $info[1]), max($info2[0], $info2[1]));

		$this->assertTrue(file_exists(MEDIA_DIR . $res['thumbnail']['media_id'] . '.media'), __LINE__);
		$info = getimagesize(MEDIA_DIR . $res['thumbnail']['media_id'] . '.media');
		$this->assertEqual(max($info[0], $info[1]), 90, __LINE__);

		$this->assertTrue(file_exists(MEDIA_DIR . $res['icon']['media_id'] . '.media'), __LINE__);
		$info = getimagesize(MEDIA_DIR . $res['icon']['media_id'] . '.media');
		$this->assertEqual(max($info[0], $info[1]), 30, __LINE__);
	}
	
	function test_create_foolproof_variations()
	{
		$files = array(
			'tmp_name' => array(
				'original' => PROJECT_DIR . 'images/1.bmp',
				'thumbnail' => '',
				'icon' => '',
			),
			'name' => array(
				'original' => 'original_name.jpg',
				'thumbnail' => '',
				'icon' => '',
			),
			'type' => array(
				'original' => 'image/bmp',
				'thumbnail' => '',
				'icon' => '',
			),
		);
		
		$this->obj->set_attribute('files_data', $files);
		$this->obj->set_attribute('id', 100);

		$this->obj->set_attribute('original_action', 'upload');

		$this->obj->set_attribute('thumbnail_action', 'generate');
		$this->obj->set_attribute('thumbnail_base_variation', 'original');
		$this->obj->set_attribute('generate_thumbnail_max_size', 100);
		
		$this->obj->expectOnce('_check_result');
		
		$affected_variations['original']['saved'] = true;
		$affected_variations['thumbnail']['saved'] = false;
		$affected_variations['thumbnail']['resized'] = false;
		
		$this->obj->expectArguments('_check_result', 
			array($affected_variations));
		
		$this->obj->create_variations();
					
		$this->db->sql_select('image_variation');
		
		$arr = $this->db->get_array();

		$this->assertTrue(is_array($arr), __LINE__);
		$this->assertEqual(sizeof($arr), 1, __LINE__);
		$this->assertEqual($arr[0]['image_id'], 100, __LINE__);
		
		$sql = "SELECT m.id as media_id, iv.variation
						FROM image_variation iv, media m
						WHERE 
							iv.media_id=m.id AND
							iv.image_id=100";

		$this->db->sql_exec($sql);
		
		$res = $this->db->get_array('variation');
		
		$this->assertTrue(file_exists(MEDIA_DIR . $res['original']['media_id'] . '.media'), __LINE__);
		$this->assertEqual(filesize(MEDIA_DIR . $res['original']['media_id'] . '.media'), filesize($files['tmp_name']['original']), __LINE__);
		$info = getimagesize(MEDIA_DIR . $res['original']['media_id'] . '.media');
		$info2 = getimagesize($files['tmp_name']['original']);
		$this->assertEqual(max($info[0], $info[1]), max($info2[0], $info2[1]));
	}
		
	function test_update_foolproof_variations()
	{
		$this->test_create_foolproof_variations();
		
		$this->obj->clearHistory();
		
		$files = array(
			'tmp_name' => array(
				'original' => PROJECT_DIR . 'images/1.jpg',
				'thumbnail' => '',
				'icon' => '',
			),
			'name' => array(
				'original' => 'original_name.jpg',
				'thumbnail' => '',
				'icon' => '',
			),
			'type' => array(
				'original' => 'image/jpeg',
				'thumbnail' => '',
				'icon' => '',
			),
		);
		
		$this->obj->set_attribute('files_data', $files);
		$this->obj->set_attribute('id', 100);

		$this->obj->set_attribute('original_action', 'upload');

		$this->obj->set_attribute('thumbnail_action', 'generate');
		$this->obj->set_attribute('thumbnail_base_variation', 'original');
		$this->obj->set_attribute('generate_thumbnail_max_size', 90);
		
		$this->obj->expectOnce('_check_result');
		
		$affected_variations['original']['saved'] = true;
		$affected_variations['thumbnail']['saved'] = true;
		$affected_variations['thumbnail']['resized'] = true;
		
		$this->obj->expectArguments('_check_result', 
			array($affected_variations));

		$this->obj->update_variations();
					
		$this->db->sql_select('image_variation');
		
		$arr = $this->db->get_array();

		$this->assertTrue(is_array($arr), __LINE__);
		$this->assertEqual(sizeof($arr), 2, __LINE__);
		$this->assertEqual($arr[0]['image_id'], 100, __LINE__);
		
		$sql = "SELECT m.id as media_id, iv.variation
						FROM image_variation iv, media m
						WHERE 
							iv.media_id=m.id AND
							iv.image_id=100";

		$this->db->sql_exec($sql);
		
		$res = $this->db->get_array('variation');
		
		$this->assertTrue(file_exists(MEDIA_DIR . $res['original']['media_id'] . '.media'), __LINE__);
		$this->assertEqual(filesize(MEDIA_DIR . $res['original']['media_id'] . '.media'), filesize($files['tmp_name']['original']), __LINE__);
		$info = getimagesize(MEDIA_DIR . $res['original']['media_id'] . '.media');
		$info2 = getimagesize($files['tmp_name']['original']);
		$this->assertEqual(max($info[0], $info[1]), max($info2[0], $info2[1]));

		$this->assertTrue(file_exists(MEDIA_DIR . $res['thumbnail']['media_id'] . '.media'), __LINE__);
		$info = getimagesize(MEDIA_DIR . $res['thumbnail']['media_id'] . '.media');
		$this->assertEqual(max($info[0], $info[1]), 90, __LINE__);
	}
}

?>