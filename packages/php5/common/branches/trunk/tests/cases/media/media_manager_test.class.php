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
require_once(dirname(__FILE__) . '/../../../media_manager.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');

class media_manager_test extends LimbTestCase 
{ 
	var $db;
  var $manager;
  
  function setUp()
  {
  	$this->db = db_factory :: instance();
  	
  	$this->_clean_up();
  	
  	$this->manager = new MediaManager();
  }
  
  function tearDown()
  { 
  	$this->_clean_up();
  }
  
  function _clean_up()
  {
    $this->db->sql_delete('media');
    fs :: rm(MEDIA_DIR);
  }
  
  function test_get_media_id_file_path()
  {
    $id = 'test';
    $this->assertEqual($this->manager->getMediaIdFilePath($id),
                       MEDIA_DIR . $id . '.media');
  }
  
  function test_create_media_record_failed_no_such_file()
  {
    try
    {
      $this->manager->createMediaRecord($disk_file_path = 'no_such_file', 
                                        $file_name = 'test file', 
                                        $mime_type = 'test mime type');
      $this->assertTrue(false);
    }
    catch(FileNotFoundException $e){}
  }

  function test_create_media_record_ok()
  {
    $media_record = $this->manager->createMediaRecord($disk_file_path = dirname(__FILE__) . '/1.jpg', 
                                      $file_name = 'test file', 
                                      $mime_type = 'image/jpeg');
    
    $this->assertEqual($media_record['file_name'], $file_name);
    $this->assertEqual($media_record['mime_type'], $mime_type);
    $this->assertEqual($media_record['etag'], md5_file($disk_file_path));
    $this->assertEqual($media_record['size'], filesize($disk_file_path));
    $this->assertTrue(isset($media_record['id']));
    
    $media_id = $media_record['id'];
    
    $db_table = Limb :: toolkit()->createDBTable('media');
    $records = $db_table->get_list();
    
    $this->assertTrue(sizeof($records), 1);
    $record = reset($records);
    $this->assertEqual($record['id'], $media_id);
    $this->assertEqual($record['file_name'], $media_record['file_name']);
    $this->assertEqual($record['mime_type'], $media_record['mime_type']);
    $this->assertEqual($record['etag'], $media_record['etag']);
    $this->assertEqual($record['size'], $media_record['size']);
    
    $this->assertTrue(file_exists($media_file_path = MEDIA_DIR . '/'. $media_id . '.media'));
    
    unlink($media_file_path);
  }

  function test_update_media_record_failed_no_such_file()
  {
    try
    {
      $this->manager->updateMediaRecord($media_id = 100, 
                                                 $disk_file_path = 'no_such_file',
                                                 $file_name = 'file name',
                                                 $mime_type = 'mime type');
      $this->assertTrue(false);
    }
    catch(FileNotFoundException $e){}
  }

  function test_update_media_record_ok()
  {
    $db_table = Limb :: toolkit()->createDBTable('media');
    
    $db_table->insert(array('id' => $media_id = 100,
                            'file_name' => 'old_file_name'));
    
    fs :: mkdir(MEDIA_DIR);
    
    $media_id = 100; 
    copy($disk_file_path = dirname(__FILE__) . '/1.jpg', MEDIA_DIR . '/'. $media_id . '.media');

    $media_record = $this->manager->updateMediaRecord($media_id, 
                                               $disk_file_path = dirname(__FILE__) . '/2.gif',
                                               $file_name = 'file name',
                                               $mime_type = 'mime type');
    

    $records = $db_table->get_list();

    $this->assertTrue(sizeof($records), 1);
    $record = reset($records);
    $this->assertEqual($record['id'], $media_id);
    $this->assertEqual($record['file_name'], $media_record['file_name']);
    $this->assertEqual($record['mime_type'], $media_record['mime_type']);
    $this->assertEqual($media_record['etag'], md5_file($disk_file_path));
    $this->assertEqual($media_record['size'], filesize($disk_file_path));
    $this->assertEqual($record['etag'], md5_file($disk_file_path));
    $this->assertEqual($record['size'], filesize($disk_file_path));
    
    $this->assertEqual(md5_file(dirname(__FILE__) . '/2.gif'), 
                       md5_file(MEDIA_DIR . '/'. $media_id . '.media'));
  }
}

?>