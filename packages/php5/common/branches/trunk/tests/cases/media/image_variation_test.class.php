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
require_once(dirname(__FILE__) . '/../../../image_variation.class.php');
require_once(dirname(__FILE__) . '/../../../media_manager.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');

Mock :: generate('image_object');
Mock :: generate('MediaManager');
Mock :: generatePartial('image_variation', 
                        'image_variation_test_version',
                        array('_get_media_manager'));

Mock :: generatePartial('image_variation',
                        'image_variation_resize_test_version',
                        array('store', '_get_media_manager'));

class image_variation_test extends LimbTestCase 
{ 
	var $db;
	var $variation;
  var $image_object;
  var $media_manager;
  
  function setUp()
  {
  	$this->db = db_factory :: instance();
  	
  	$this->_clean_up();
  	
    $this->media_manager = new MockMediaManager($this);
    
  	$this->variation = new image_variation_test_version($this);
    $this->variation->__construct();
    $this->variation->setReturnValue('_get_media_manager', $this->media_manager);
    
    $this->image_object = new Mockimage_object($this);  	
  }
  
  function tearDown()
  { 
  	$this->_clean_up();
    
    $this->image_object->tally();
    $this->media_manager->tally();
  }
  
  function _clean_up()
  {
    $this->db->sql_delete('image_variation');
    $this->db->sql_delete('media');
    fs :: rm(MEDIA_DIR);
  }
    
  function test_attach_variation()
  {
    $this->image_object->expectOnce('get_id');
    $this->image_object->setReturnValue('get_id', 100);
    
    $this->variation->attach_to_image_object($this->image_object);
    
    $this->assertEqual(100, $this->variation->get_image_id());
  }
  
  function test_store_no_input_file_no_variation()
  {
    try
    {
      $this->variation->store();
      $this->assertTrue(false);
    }
    catch(LimbException $e){}
  }
        
  function test_store_new_input_file_no_variation()
  {
    $this->variation->set_name($variation_name = 'original');    
    $this->variation->set_image_id($image_id = 100);
    $this->variation->set_input_file($file = dirname(__FILE__) . '/1.jpg');
    $this->variation->set_file_name($file_name = 'test file');
    $this->variation->set_mime_type($mime_type = 'image/jpeg');
    
    $this->media_manager->expectOnce('createMediaRecord', array($file, $file_name, $mime_type));
    $this->media_manager->setReturnValue('createMediaRecord', 
                                         array('id' => $media_id = 'some_media_id',
                                               'etag' => $etag = 'some etag',
                                               'size' => $size = 1020));
    
    $this->variation->store();

    $this->assertNull($this->variation->get_input_file()); 
										
    $result = $this->_get_object_db_variations($image_id);
    
    $this->assertEqual(sizeof($result), 1);    
    $variation = $result[$variation_name];
		 
    $this->assertEqual($variation['image_id'], $image_id);
    $this->assertEqual($variation['media_id'], $media_id);
    $this->assertEqual($variation['width'], 100);
    $this->assertEqual($variation['height'], 137);
    
    $this->assertEqual($this->variation->get_file_name(), $file_name);
    $this->assertEqual($this->variation->get_etag(), $etag);
    $this->assertEqual($this->variation->get_size(), $size);
	}

  function test_store_new_input_file_for_existing_variation()
  {
    $this->db->sql_insert('image_variation', array('id' => $id = 1000,
                                                   'media_id' => $media_id = '10sdsdszx210', 
                                                   'image_id' => $image_id = 100, 
                                                   'variation' => 'whatever'));
    $this->db->sql_insert('media', array('id' => $media_id, 
                                         'file_name' => 'file1', 
                                         'mime_type' => 'type1', 
                                         'size' => 20, 
                                         'etag' => 'etag1'));
    
    $this->variation->set_id($id);
    $this->variation->set_name($variation_name = 'original');        
    $this->variation->set_image_id($image_id);
    $this->variation->set_media_id($media_id);
    $this->variation->set_input_file($file = dirname(__FILE__) . '/1.jpg');
    $this->variation->set_file_name($file_name = 'test file');
    $this->variation->set_mime_type($mime_type = 'image/jpeg');

    $this->media_manager->expectOnce('updateMediaRecord', 
                                     array($media_id, $file, $file_name, $mime_type));
    
    $this->media_manager->setReturnValue('updateMediaRecord', 
                                         array('id' => $media_id,
                                               $file,
                                               'etag' => $etag = 'some etag',
                                               'size' => $size = 1020));

    $this->variation->store();

    $this->assertNull($this->variation->get_input_file()); 
										
    $result = $this->_get_object_db_variations($image_id);
    
    $this->assertEqual(sizeof($result), 1);    
    $variation = $result[$variation_name];
    
    $this->assertEqual($variation['image_id'], $image_id);
    $this->assertEqual($variation['media_id'], $media_id);
    $this->assertEqual($variation['width'], 100);
    $this->assertEqual($variation['height'], 137);
    
    $this->assertEqual($this->variation->get_file_name(), $file_name);
    $this->assertEqual($this->variation->get_etag(), $etag);
    $this->assertEqual($this->variation->get_size(), $size);
	}
  
  function test_resize_new_input_file()
  {
    $variation = new image_variation_resize_test_version($this);
    $variation->__construct();
    
    $variation->set_name('original');    
    $variation->set_image_id($image_id = 100);
    
    $variation->set_input_file($file = dirname(__FILE__) . '/1.jpg');
    $variation->set_file_name($file_name = 'test file');
    $variation->set_mime_type($mime_type = 'image/jpeg');

    $variation->expectOnce('store');

    $variation->resize($max_size = 30);
    
    $this->assertNotNull($variation->get_input_file());
    $this->assertNotEqual($variation->get_input_file(), $file);
    
    $variation->tally();
	}
  
  function test_resize_existing()
  {
    $variation = new image_variation_resize_test_version($this);
    $variation->__construct();
    $variation->setReturnValue('_get_media_manager', $this->media_manager);
    
    $variation->set_mime_type($mime_type = 'image/gif');

    $this->media_manager->setReturnValue('getMediaIdFilePath', $file = dirname(__FILE__) . '/2.gif');  

    $variation->expectOnce('store');
    
    $variation->resize($max_size = 30);
    
    $this->assertNotNull($variation->get_input_file());
    $this->assertNotEqual($variation->get_input_file(), $file);
    
    $variation->tally();
	}
 
  function _get_object_db_variations($id)
  {
		$sql = "SELECT * FROM image_variation  
						WHERE 
						image_id={$id}";

		$this->db->sql_exec($sql);
		
		return $this->db->get_array('variation');
  }
  
}

?>