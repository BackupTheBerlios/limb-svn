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
require_once(dirname(__FILE__) . '/../../../site_objects/image_object.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');

Mock :: generatePartial('image_object',
                        'image_object_manipulation_test_version',
                        array('_do_parent_create', '_do_parent_fetch'));

class image_object_manipulation_test extends LimbTestCase 
{ 
	var $db;
	var $object;  
  var $toolkit;
  
  function setUp()
  {
  	$this->db = db_factory :: instance();
  	
  	$this->_clean_up();
  	
  	$this->object = new image_object_manipulation_test_version($this);
    $this->object->__construct();  	
  }
  
  function tearDown()
  { 
  	$this->_clean_up();
  	
    $this->object->tally();
  }
  
  function _clean_up()
  {
  	$this->db->sql_delete('sys_site_object');
    $this->db->sql_delete('image_variation');
    $this->db->sql_delete('image_object');
    $this->db->sql_delete('media');
    fs :: rm(MEDIA_DIR);
  }
  
  /*function test_get_empty_variations()
  {
    $this->assertTrue(!$this->object->get_variations());
  }

  function test_get_null_variation()
  {
    $this->assertNull($this->object->get_variation('original'));
  } 
  
  function test_merge()//simplify to mocks later
  {            
    $raw_data = array('variations' => 
                      array('original' => 
                            array('id' => $id1 = 1000,
                                  'image_id' => $image_id1 = 1,
                                  'media_id' => $media_id1 = 10,
                                  'width' => $width1 = 100,
                                  'height' => $height1 = 200,
                                  'size' => $size1 = 300,
                                  'mime_type' => $type1 = 'type1',
                                  'file_name' => $name1 = 'name1',
                                  'etag' => $etag1 = 'yo-yo'
                                  ),
                            'icon' =>
                            array('id' => $id2 = 2000,
                                  'image_id' => $image_id2 = 2,
                                  'media_id' => $media_id2 = 20,
                                  'width' => $width2 = 200,
                                  'height' => $height2 = 300,
                                  'size' => $size2 = 30,
                                  'mime_type' => $type2 = 'type2',
                                  'file_name' => $name2 = 'name2',
                                  'etag' => $etag2 = 'wow-wow'
                                  ),                            
                            )
                      );
    
    $this->object->merge($raw_data);
    
    $vars = $this->object->get_variations();
    
    $this->assertEqual(sizeof($vars), 2);
    $this->assertIsA($vars['original'], 'image_variation');
    $this->assertIsA($vars['icon'], 'image_variation');
    
    $this->assertEqual($vars['original']->get_id(), $id1);
    $this->assertEqual($vars['original']->get_image_id(), $image_id1);
  }
    
  function test_setters_getters()
  {  
    $this->object->set_variation_file_path('original', 'test1');
    $this->assertEqual($this->object->get_variation_file_path('original'), 'test1');

    $this->object->set_variation_file_name('original', 'test2');
    $this->assertEqual($this->object->get_variation_file_name('original'), 'test2');

    $this->object->set_variation_mime_type('original', 'test3');
    $this->assertEqual($this->object->get_variation_mime_type('original'), 'test3');

    $this->object->set_variation_mime_type('original', 'test3');
    $this->assertEqual($this->object->get_variation_mime_type('original'), 'test3');
  } 
  
  function test_upload_variation_insufficient_data1()
  {
    $this->object->set_id($id = 100);
    
    try
    {
      $this->object->upload_variation('original');
      $this->assertTrue(false);
    }
    catch(LimbException $e){}    
  }
  
  function test_upload_variation_insufficient_data2()
  {
    $this->object->set_id($id = 100);
    $this->object->set_variation_file_path('original', 'whatever');
    
    try
    {      
      $this->object->upload_variation('original');
      $this->assertTrue(false);
    }
    catch(LimbException $e){}
  }
  
  function test_upload_variation_insufficient_data3()
  {
    $this->object->set_id($id = 100);
    $this->object->set_variation_file_path('original', 'whatever');
    $this->object->set_variation_file_name('original', 'whatever');
    
    try
    {      
      $this->object->upload_variation('original');
      $this->assertTrue(false);
    }
    catch(LimbException $e){}
  }
  
  function test_upload_variation_no_size_limit()
  {
    $this->object->set_id($id = 100);
    
  	$this->object->set_variation_file_path('original', $file = dirname(__FILE__) . '/1.jpg');
    $this->object->set_variation_file_name('original', 'original.jpg');
    $this->object->set_variation_mime_type('original', 'image/jpeg');      						
				
		$this->object->upload_variation('original');
										
    $res = $this->_get_object_db_variations($id);
    $variation = $res['original'];
		 
		$this->assertTrue(file_exists(MEDIA_DIR . $variation['media_id'] . '.media'));
    $this->assertEqual($variation['size'], filesize(MEDIA_DIR . $variation['media_id'] . '.media'));
		$this->assertEqual(filesize(MEDIA_DIR . $variation['media_id'] . '.media'), 
                       filesize($file));    
	}

  function test_upload_variation_with_size_limit()
  {
    $this->object->set_id($id = 100);
    
  	$this->object->set_variation_file_path('original', $file = dirname(__FILE__) . '/1.jpg');
    $this->object->set_variation_file_name('original', 'original.jpg');
    $this->object->set_variation_mime_type('original', 'image/jpeg');      						
				
		$this->object->upload_variation('original', $max_size = 30);
										
    $res = $this->_get_object_db_variations($id);
    $variation = $res['original'];
		 
		$this->assertTrue(file_exists(MEDIA_DIR . $variation['media_id'] . '.media'));
    $this->assertEqual($variation['size'], filesize(MEDIA_DIR . $variation['media_id'] . '.media'));
	}
  
  function test_generate_nonexisting_variation()
  {
    $this->object->set_id($id = 100);
    
  	$this->object->set_variation_file_path('original', $file = dirname(__FILE__) . '/1.jpg');
    $this->object->set_variation_file_name('original', 'original.jpg');
    $this->object->set_variation_mime_type('original', 'image/jpeg');
    
    $this->object->upload_variation('original');
 
		$this->object->generate_variation('original', 'thumbnail', 30);
										
    $res = $this->_get_object_db_variations($id);
    $thumbnail = $res['thumbnail'];
    $original = $res['original'];
		 
		$this->assertTrue(file_exists(MEDIA_DIR . $thumbnail['media_id'] . '.media'));
    $this->assertEqual($thumbnail['size'], filesize(MEDIA_DIR . $thumbnail['media_id'] . '.media'));
    $this->assertEqual($original['file_name'], $thumbnail['file_name']);
  }

  function test_generate_existing_variation()
  {
    $this->object->set_id($id = 100);
    
  	$this->object->set_variation_file_path('original', $file = dirname(__FILE__) . '/1.jpg');
    $this->object->set_variation_file_name('original', 'original.jpg');
    $this->object->set_variation_mime_type('original', 'image/jpeg');
    
    $this->object->upload_variation('original');

    $res = $this->_get_object_db_variations($id);
    $variation1 = $res['original'];

		$this->object->generate_variation('original', 'original', 30);
										
    $res = $this->_get_object_db_variations($id);
    $variation2 = $res['original'];    
		 
		$this->assertTrue(file_exists(MEDIA_DIR . $variation2['media_id'] . '.media'));
    $this->assertEqual($variation2['size'], filesize(MEDIA_DIR . $variation2['media_id'] . '.media'));
    $this->assertTrue($variation1['size'] > $variation2['size']);
    $this->assertEqual($variation2['file_name'], $variation1['file_name']);
  }
  
  function test_fetch_empty()
  {
    $this->object->setReturnValue('_do_parent_fetch', array());
    
    $this->assertTrue(!$this->object->fetch());
  }*/
  
  function _get_object_db_variations($id)
  {
		$sql = "SELECT 
            m.id as media_id,
            m.file_name as file_name,
            m.size as size,
            iv.variation as variation            
						FROM 
            image_variation iv, 
            media m
						WHERE 
						iv.media_id=m.id AND
						iv.image_id={$id}";

		$this->db->sql_exec($sql);
		
		return $this->db->get_array('variation');
  }
  
}

?>