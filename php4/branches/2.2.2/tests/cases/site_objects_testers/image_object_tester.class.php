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
require_once(LIMB_DIR . 'core/lib/system/fs.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'core/model/site_objects/image_object.class.php');
require_once(LIMB_DIR . '/tests/cases/site_objects_testers/site_object_tester.class.php');

Mock::generatePartial
(
  'image_object',
  'image_object_test_version',
  array('_check_result', '_get_db_table_name')
); 
  
class image_object_tester extends site_object_tester 
{
  function image_object_tester() 
  {
  	parent :: site_object_tester('image_object');
  }

  function & _create_site_object()
  {
  	$obj =& new image_object_test_version($this);
  	$obj->setReturnValue('_get_db_table_name', 'image_object');
  	
  	$obj->image_object();
  	
  	return $obj;
  }
  	
	function tearDown()
	{	
		parent :: tearDown();
		
		$this->object->tally();
	} 
      
  function _clean_up()
  {
  	parent :: _clean_up();
  	
		fs :: rm(MEDIA_DIR);
		$this->db->sql_delete('image_object');
  	$this->db->sql_delete('image_variation');
  	$this->db->sql_delete('media');
  }
        
  function test_create_variations()
  {
  	$files = array(
			'tmp_name' => array(
				'original' => LIMB_DIR . '/tests/cases/site_objects_testers/images/1.jpg',
				'thumbnail' => LIMB_DIR . '/tests/cases/site_objects_testers/images/2.jpg',
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
  	
  	$this->object->set_attribute('files_data', $files);
  						
		$this->object->set_attribute('id', 100);

		$this->object->set_attribute('original_action', 'upload');

		$this->object->set_attribute('thumbnail_action', 'upload');
		$this->object->set_attribute('upload_thumbnail_max_size', 100);
		
		$this->object->set_attribute('icon_action', 'generate');
		$this->object->set_attribute('icon_base_variation', 'thumbnail');
		$this->object->set_attribute('generate_icon_max_size', 50);

		$this->object->expectOnce('_check_result');
		
		$affected_variations['original']['saved'] = true;
		$affected_variations['thumbnail']['saved'] = true;
		$affected_variations['thumbnail']['resized'] = true;
		$affected_variations['icon']['saved'] = true;
		$affected_variations['icon']['resized'] = true;
		
		$this->object->expectArguments('_check_result', 
			array($affected_variations));
		
		$this->object->create_variations();
								
		$this->db->sql_select('image_variation');
		
		$arr = $this->db->get_array();
		
		$this->assertTrue(is_array($arr), __LINE__ . ' %s');
		$this->assertEqual(sizeof($arr), 3, __LINE__ . ' %s');
		$this->assertEqual($arr[0]['image_id'], 100, __LINE__ . ' %s');
		
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
  	
  	$this->object->clearHistory();
  	
		$files = array(
			'tmp_name' => array(
				'original' => LIMB_DIR . '/tests/cases/site_objects_testers/images/1.jpg',
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
		
		$this->object->set_attribute('files_data', $files);
		$this->object->set_attribute('id', 100);

		$this->object->set_attribute('original_action', 'upload');

		$this->object->set_attribute('thumbnail_action', 'generate');
		$this->object->set_attribute('thumbnail_base_variation', 'thumbnail');
		$this->object->set_attribute('generate_thumbnail_max_size', 90);
		
		$this->object->set_attribute('icon_action', 'generate');
		$this->object->set_attribute('icon_base_variation', 'original');
		$this->object->set_attribute('generate_icon_max_size', 30);
		
		$this->object->expectOnce('_check_result');
		
		$affected_variations['original']['saved'] = true;
		$affected_variations['thumbnail']['saved'] = true;
		$affected_variations['thumbnail']['resized'] = true;
		$affected_variations['icon']['saved'] = true;
		$affected_variations['icon']['resized'] = true;
		
		$this->object->expectArguments('_check_result', 
			array($affected_variations));

		$this->object->update_variations();
				
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
				'original' => LIMB_DIR . '/tests/cases/site_objects_testers/images/1.bmp',
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
		
		$this->object->set_attribute('files_data', $files);
		$this->object->set_attribute('id', 100);

		$this->object->set_attribute('original_action', 'upload');

		$this->object->set_attribute('thumbnail_action', 'generate');
		$this->object->set_attribute('thumbnail_base_variation', 'original');
		$this->object->set_attribute('generate_thumbnail_max_size', 100);
		
		$this->object->expectOnce('_check_result');
		
		$affected_variations['original']['saved'] = true;
		$affected_variations['thumbnail']['saved'] = false;
		$affected_variations['thumbnail']['resized'] = false;
		
		$this->object->expectArguments('_check_result', 
			array($affected_variations));
		
		$this->object->create_variations();
					
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
		
		$this->object->clearHistory();
		
		$files = array(
			'tmp_name' => array(
				'original' => LIMB_DIR . '/tests/cases/site_objects_testers/images/1.jpg',
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
		
		$this->object->set_attribute('files_data', $files);
		$this->object->set_attribute('id', 100);

		$this->object->set_attribute('original_action', 'upload');

		$this->object->set_attribute('thumbnail_action', 'generate');
		$this->object->set_attribute('thumbnail_base_variation', 'original');
		$this->object->set_attribute('generate_thumbnail_max_size', 90);
		
		$this->object->expectOnce('_check_result');
		
		$affected_variations['original']['saved'] = true;
		$affected_variations['thumbnail']['saved'] = true;
		$affected_variations['thumbnail']['resized'] = true;
		
		$this->object->expectArguments('_check_result', 
			array($affected_variations));

		$this->object->update_variations();
					
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
	
	function test_fetch()
	{
		 $files = array(
			'tmp_name' => array(
				'original' => LIMB_DIR . '/tests/cases/site_objects_testers/images/1.jpg',
				'thumbnail' => LIMB_DIR . '/tests/cases/site_objects_testers/images/2.jpg',
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
  	
		$this->object->set_attribute('files_data', $files);
  						
		$this->object->set_attribute('original_action', 'upload');
		$this->object->set_attribute('thumbnail_action', 'upload');
		$this->object->set_attribute('icon_action', 'generate');
		
		$this->object->set_attribute('upload_thumbnail_max_size', 100);
		$this->object->set_attribute('icon_base_variation', 'thumbnail');
		$this->object->set_attribute('generate_icon_max_size', 50);
		
		parent :: test_fetch();
	}
	
	function _compare_fetch_data($record)
	{
		parent :: _compare_fetch_data($record);
		
		$this->assertEqual(sizeof($record['variations']), 3);
		/*$this->assertEqual($record['variations']['original']['file_name'], 'original_name.jpg');
		$this->assertEqual($record['variations']['thumbnail']['file_name'], 'thumbnail_name.jpg');
		$this->assertEqual($record['variations']['icon']['file_name'], 'thumbnail_name.icon.jpg');*/
		
		$variation_data = $this->object->get_variation_media_data('original');
		$this->assertEqual($variation_data, $record['variations']['original']);
	}

}

?>