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
require_once(dirname(__FILE__) . '/../../../finders/image_objects_raw_finder.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');

Mock :: generatePartial('image_objects_raw_finder',
                        'image_objects_raw_finder_test_version',
                        array('_do_parent_find'));

class image_objects_raw_finder_test extends LimbTestCase 
{ 
	var $db;
	var $finder;  
  
  function setUp()
  {
  	$this->db = db_factory :: instance();
  	
  	$this->_clean_up();
  	
  	$this->finder = new image_objects_raw_finder_test_version($this);
  }
  
  function tearDown()
  { 
  	$this->_clean_up();
  	
    $this->finder->tally();
  }
  
  function _clean_up()
  {
    $this->db->sql_delete('image_variation');
    $this->db->sql_delete('media');    
  }
  
  function test_find_empty()
  {
    $this->finder->setReturnValue('_do_parent_find', array(), array(array(), array()));
    
    $this->assertTrue(!$this->finder->find());
  }

  function test_find_with_variations()
  {
    $this->db->sql_insert('image_variation', array('id' => $id1 = 1000,
                                                   'media_id' => $media_id1 = '10sdsdszx210', 
                                                   'image_id' => $image_id1 = 10, 
                                                   'variation' => $name1 = 'original',
                                                   'width' => $width1 = 20,
                                                   'height' => $height1 = 40));

    $this->db->sql_insert('image_variation', array('id' => $id2 = 2000,
                                                   'media_id' => $media_id2 = '20sdcHJszx21', 
                                                   'image_id' => $image_id2 = 20, 
                                                   'variation' => $name2 = 'icon',
                                                   'width' => $width2 = 100,
                                                   'height' => $height2 = 200));

    $this->db->sql_insert('image_variation', array('id' => $id3 = 3000,
                                                   'media_id' => $media_id3 = '20sdJkcHJszx21', 
                                                   'image_id' => $image_id2,//note this 
                                                   'variation' => $name3 = 'thumbnail',
                                                   'width' => $width3 = 130,
                                                   'height' => $height3 = 250));
    
    $this->db->sql_insert('media', array('id' => $media_id1, 
                                         'file_name' => $file_name1 = 'file1', 
                                         'mime_type' => $mime_type1 = 'type1', 
                                         'size' => $size1 = 20, 
                                         'etag' => $etag1 = 'etag1'));

    $this->db->sql_insert('media', array('id' => $media_id2, 
                                         'file_name' => $file_name2 = 'file2', 
                                         'mime_type' => $mime_type2 = 'type2', 
                                         'size' => $size2 = 30, 
                                         'etag' => $etag2 = 'etag2'));
    
    $this->db->sql_insert('media', array('id' => $media_id3, 
                                         'file_name' => $file_name3 = 'file3', 
                                         'mime_type' => $mime_type3 = 'type3', 
                                         'size' => $size3 = 340, 
                                         'etag' => $etag3 = 'etag3'));
    
    
    $objects = array(array('object_id' => 10),
                     array('object_id' => 20));
    
    $params = 'some params';
    $sql_params = 'some sql params';
    
    $this->finder->expectOnce('_do_parent_find', array($params, $sql_params));
    $this->finder->setReturnValue('_do_parent_find', $objects, array($params, $sql_params));
    
    $result = array(array('object_id' => 10, 
                          'variations' => array('original' => array('id' => $id1,
                                                                    'media_id' => $media_id1, 
                                                                    'image_id' => $image_id1,
                                                                    'variation' => $name1,
                                                                    'width' => $width1,
                                                                    'height' => $height1,
                                                                    'size' => $size1,
                                                                    'mime_type' => $mime_type1,
                                                                    'file_name' => $file_name1,
                                                                    'etag' => $etag1))),
                    array('object_id' => 20,
                          'variations' => array('icon' => array('id' => $id2,
                                                                'media_id' => $media_id2, 
                                                                'image_id' => $image_id2,
                                                                'variation' => $name2,
                                                                'width' => $width2,
                                                                'height' => $height2,
                                                                'size' => $size2,
                                                                'mime_type' => $mime_type2,
                                                                'file_name' => $file_name2,
                                                                'etag' => $etag2),
                                                'thumbnail' => array('id' => $id3,
                                                                'media_id' => $media_id3, 
                                                                'image_id' => $image_id2,//note this
                                                                'variation' => $name3,
                                                                'width' => $width3,
                                                                'height' => $height3,
                                                                'size' => $size3,
                                                                'mime_type' => $mime_type3,
                                                                'file_name' => $file_name3,
                                                                'etag' => $etag3))));
    
    $this->assertEqual($this->finder->find($params, $sql_params), $result);
  }
}

?>