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
require_once(dirname(__FILE__) . '/../../../finders/image_objects_raw_finder.class.php');
require_once(dirname(__FILE__) . '/../../../data_mappers/image_object_mapper.class.php');
require_once(dirname(__FILE__) . '/../../../image_variation.class.php');
require_once(dirname(__FILE__) . '/../../../media_manager.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');

Mock :: generatePartial('image_object_mapper',
                        'image_object_mapper_test_version',
                        array('_get_finder',
                              '_do_parent_insert',
                              '_do_parent_update',
                              '_get_media_manager'));

Mock :: generate('media_manager');
Mock :: generate('image_objects_raw_finder');

class image_object_data_mapper_test extends LimbTestCase
{
  var $db;
  var $finder;
  var $mapper;
  var $media_manager;
  var $image;

  function setUp()
  {
    $this->db = db_factory :: instance();

    $this->finder = new Mockimage_objects_raw_finder($this);
    $this->media_manager = new Mockmedia_manager($this);

    $this->mapper = new image_object_mapper_test_version($this);
    $this->mapper->setReturnValue('_get_finder', $this->finder);
    $this->mapper->setReturnValue('_get_media_manager', $this->media_manager);

    $this->_clean_up();
  }

  function tearDown()
  {
    $this->_clean_up();

    $this->mapper->tally();
    $this->finder->tally();
  }

  function _clean_up()
  {
    $this->db->sql_delete('image_object');
    $this->db->sql_delete('image_variation');
    $this->db->sql_delete('media');
  }

  function test_find_by_id()
  {
    $variations_data = array('original' => array('id' => $id1 = 200,
                                                              'name' => $name1 = 'original',
                                                              'etag' => $etag1 = 'etag1',
                                                              'width' => $width1 = 200,
                                                              'height' => $height1 = 100,
                                                              'media_id' => $media_id1 = 101,
                                                              'media_file_id' => $media_file_id1 = 'media_file_id1',
                                                              'file_name' => $file_name1 = 'file_name1',
                                                              'mime_type' => $mime_type1 = 'mime_type1',
                                                              'size' => $size1 = 500
                                                              ),
                                          'icon' => array(    'id' => $id2 = 300,
                                                              'name' => $name2 = 'icon',
                                                              'etag' => $etag2 = 'etag2',
                                                              'width' => $width2 = 20,
                                                              'height' => $height2 = 10,
                                                              'media_id' => $media_id2 = 102,
                                                              'media_file_id' => $media_file_id2 = 'media_file_id2',
                                                              'file_name' => $file_name2 = 'file_name2',
                                                              'mime_type' => $mime_type2 = 'mime_type2',
                                                              'size' => $size2 = 50
                                                              ),

                                          );
    $result = array('id' => $id = 100,
                    'description' => $description = 'Description',
                    'variations' => $variations_data

    );

    $this->finder->expectOnce('find_by_id', array($id));
    $this->finder->setReturnValue('find_by_id', $result, array($id));

    $image = $this->mapper->find_by_id($id);

    $this->assertEqual($image->get_id(), $id);
    $this->assertEqual($image->get_description(), $description);

    $this->_check_image_object_variations($image, $variations_data);
  }

  function test_insert()
  {
    $image = new image_object();
    $image->set_description($description = 'some description');

    $image_variation1 = new image_variation();
    $image_variation1->set_width($width1 = 50);
    $image_variation1->set_height($height1 = 100);
    $image_variation1->set_media_file_id($media_file_id1 = 'dsada');
    $image_variation1->set_name($name1 = 'original');
    $image_variation1->set_etag($etag1 = 'dsajadhk');
    $image_variation1->set_mime_type($mime_type1 = 'jpeg');
    $image_variation1->set_size($size1 = 500);
    $image_variation1->set_file_name($file_name1 = 'some file');

    $image->attach_variation($image_variation1);

    $image_variation2 = new image_variation();
    $image_variation2->set_width($width2 = 100);
    $image_variation2->set_height($height2 = 200);
    $image_variation2->set_media_file_id($media_file_id2 = 'dsfsdf');
    $image_variation2->set_name($name2 = 'icon');
    $image_variation2->set_etag($etag2 = 'dsajrwek');
    $image_variation2->set_mime_type($mime_type2 = 'png');
    $image_variation2->set_size($size2 = 500);
    $image_variation2->set_file_name($file_name2 = 'some file2');

    $image->attach_variation($image_variation2);

    $this->mapper->expectOnce('_do_parent_insert', array($image));
    $this->mapper->insert($image);

    $this->db->sql_select('media');
    $media_rows = $this->db->get_array();

    $media1 = reset($media_rows);
    $this->assertEqual($media1['id'], $image_variation1->get_media_id());
    $this->assertEqual($media1['media_file_id'], $media_file_id1);
    $this->assertEqual($media1['file_name'], $file_name1);
    $this->assertEqual($media1['mime_type'], $mime_type1);
    $this->assertEqual($media1['size'], $size1);
    $this->assertEqual($media1['etag'], $etag1);

    $media2 = next($media_rows);
    $this->assertEqual($media2['id'], $image_variation2->get_media_id());
    $this->assertEqual($media2['media_file_id'], $media_file_id2);
    $this->assertEqual($media2['file_name'], $file_name2);
    $this->assertEqual($media2['mime_type'], $mime_type2);
    $this->assertEqual($media2['size'], $size2);
    $this->assertEqual($media2['etag'], $etag2);

    $this->db->sql_select('image_variation');
    $variation_rows = $this->db->get_array();
    $variation_data1 = reset($variation_rows);
    $this->assertEqual($variation_data1['image_id'], $image->get_id());
    $this->assertEqual($variation_data1['media_id'], $image_variation1->get_media_id());
    $this->assertEqual($variation_data1['width'], $width1);
    $this->assertEqual($variation_data1['height'], $height1);
    $this->assertEqual($variation_data1['variation'], $name1);

    $variation_data2 = next($variation_rows);
    $this->assertEqual($variation_data2['image_id'], $image->get_id());
    $this->assertEqual($variation_data2['media_id'], $image_variation2->get_media_id());
    $this->assertEqual($variation_data2['width'], $width2);
    $this->assertEqual($variation_data2['height'], $height2);
    $this->assertEqual($variation_data2['variation'], $name2);
  }

  function test_update()
  {
    $this->db->sql_insert('image_object', array('id' => $id = 1000,
                                                'description' => 'Description'));

    $this->db->sql_insert('image_variation', array('id' => $variation_id = 1000,
                                                   'media_id' => $media_id = 101,
                                                   'image_id' => $id = 100,
                                                   'variation' => 'whatever'));

    $this->db->sql_insert('media', array('id' => $media_id,
                                         'media_file_id' => $old_media_file_id = 'sdFjfskd23923sds',
                                         'file_name' => 'file1',
                                         'mime_type' => 'type1',
                                         'size' => 20,
                                         'etag' => 'etag1'));


    $image = new image_object();
    $image->set_id($id);
    $image->set_description($description = 'some description');

    $image_variation1 = new image_variation();
    $image_variation1->set_id($variation_id);
    $image_variation1->set_media_id($media_id);
    $image_variation1->set_width($width1 = 50);
    $image_variation1->set_height($height1 = 100);
    $image_variation1->set_media_file_id($media_file_id1 = 'dsada');//note it's a new one!!!
    $image_variation1->set_name($name1 = 'original');
    $image_variation1->set_etag($etag1 = 'dsajadhk');
    $image_variation1->set_mime_type($mime_type1 = 'jpeg');
    $image_variation1->set_size($size1 = 500);
    $image_variation1->set_file_name($file_name1 = 'some file');

    $image->attach_variation($image_variation1);

    $this->media_manager->expectOnce('unlink_media', array($old_media_file_id));

    $image_variation2 = new image_variation();
    $image_variation2->set_width($width2 = 100);
    $image_variation2->set_height($height2 = 200);
    $image_variation2->set_media_file_id($media_file_id2 = 'dsfsdf');
    $image_variation2->set_name($name2 = 'icon');
    $image_variation2->set_etag($etag2 = 'dsajrwek');
    $image_variation2->set_mime_type($mime_type2 = 'png');
    $image_variation2->set_size($size2 = 500);
    $image_variation2->set_file_name($file_name2 = 'some file2');

    $image->attach_variation($image_variation2);

    $this->mapper->expectOnce('_do_parent_update', array($image));
    $this->mapper->update($image);

    $this->db->sql_select('media');
    $media_rows = $this->db->get_array();

    $this->assertEqual(sizeof($media_rows), 2);

    $media1 = reset($media_rows);
    $this->assertEqual($media1['id'], $media_id);
    $this->assertEqual($media1['media_file_id'], $media_file_id1);
    $this->assertEqual($media1['file_name'], $file_name1);
    $this->assertEqual($media1['mime_type'], $mime_type1);
    $this->assertEqual($media1['size'], $size1);
    $this->assertEqual($media1['etag'], $etag1);

    $media2 = next($media_rows);
    $this->assertEqual($media2['id'], $image_variation2->get_media_id());
    $this->assertEqual($media2['media_file_id'], $media_file_id2);
    $this->assertEqual($media2['file_name'], $file_name2);
    $this->assertEqual($media2['mime_type'], $mime_type2);
    $this->assertEqual($media2['size'], $size2);
    $this->assertEqual($media2['etag'], $etag2);

    $this->db->sql_select('image_variation');

    $variation_rows = $this->db->get_array();

    $this->assertEqual(sizeof($variation_rows), 2);

    $variation_data1 = reset($variation_rows);
    $this->assertEqual($variation_data1['image_id'], $image->get_id());
    $this->assertEqual($variation_data1['media_id'], $media_id);
    $this->assertEqual($variation_data1['width'], $width1);
    $this->assertEqual($variation_data1['height'], $height1);
    $this->assertEqual($variation_data1['variation'], $name1);

    $variation_data2 = next($variation_rows);
    $this->assertEqual($variation_data2['image_id'], $image->get_id());
    $this->assertEqual($variation_data2['media_id'], $image_variation2->get_media_id());
    $this->assertEqual($variation_data2['width'], $width2);
    $this->assertEqual($variation_data2['height'], $height2);
    $this->assertEqual($variation_data2['variation'], $name2);
  }

  function _check_image_object_variations($image, $check_array)
  {
    foreach($check_array as $variation_name => $data)
    {
      $variation = $image->get_variation($variation_name);

      foreach($data as $field => $value)
      {
        $get_method = 'get_' . $field;
        $this->assertEqual($variation->$get_method(), $value);
      }
    }
  }

}

?>