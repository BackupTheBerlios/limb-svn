<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/../../../site_objects/FileObject.class.php');
require_once(dirname(__FILE__) . '/../../../finders/FileObjectsRawFinder.class.php');
require_once(dirname(__FILE__) . '/../../../data_mappers/FileObjectMapper.class.php');
require_once(dirname(__FILE__) . '/../../../ImageVariation.class.php');
require_once(dirname(__FILE__) . '/../../../MediaManager.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');
require_once(LIMB_DIR . '/class/etc/limb_util.inc.php');

Mock :: generatePartial('FileObjectMapper',
                        'FileObjectMapperTestVersion',
                        array('_getFinder',
                              '_doParentInsert',
                              '_doParentUpdate',
                              '_getMediaManager'));

Mock :: generate('MediaManager');
Mock :: generate('FileObjectsRawFinder');

class FileObjectDataMapperTest extends LimbTestCase
{
  var $db;
  var $finder;
  var $mapper;
  var $media_manager;
  var $image;

  function setUp()
  {
    $this->db = DbFactory :: instance();

    $this->finder = new MockFileObjectsRawFinder($this);
    $this->media_manager = new MockMediaManager($this);

    $this->mapper = new FileObjectMapperTestVersion($this);
    $this->mapper->setReturnValue('_getFinder', $this->finder);
    $this->mapper->setReturnValue('_getMediaManager', $this->media_manager);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();

    $this->mapper->tally();
    $this->finder->tally();
  }

  function _cleanUp()
  {
    $this->db->sqlDelete('file_object');
    $this->db->sqlDelete('image_variation');
    $this->db->sqlDelete('media');
  }

  function testFindById()
  {
    $result = array('id' => $id = 100,
                    'description' => $description = 'Description',
                    'variations' => $variations_data);

    $this->finder->expectOnce('findById', array($id));
    $this->finder->setReturnValue('findById', $result, array($id));

    $image = $this->mapper->findById($id);

    $this->assertEqual($image->getId(), $id);
    $this->assertEqual($image->getDescription(), $description);

    $this->_checkFileObjectVariations($image, $variations_data);
  }

  function testInsert()
  {
    $file = new FileObject();

    $file->setDescription($description = 'some description');
    $file->setMediaFileId($media_file_id1 = 'dsada');
    $file->setName($name1 = 'original');
    $file->setEtag($etag1 = 'dsajadhk');
    $file->setMimeType($mime_type1 = 'jpeg');
    $file->setFileName($file_name1 = 'some file');

    $this->mapper->expectOnce('_doParentInsert', array($file));
    $this->mapper->insert($file);

    $this->db->sqlSelect('media');
    $media_rows = $this->db->getArray();

    $this->assertEqual(1, sizeof($media_rows));

    $media1 = reset($media_rows);
    $this->assertEqual($media1['id'], $file->getMediaId());
    $this->assertEqual($media1['media_file_id'], $media_file_id1);
    $this->assertEqual($media1['file_name'], $file_name1);
    $this->assertEqual($media1['mime_type'], $mime_type1);
    $this->assertEqual($media1['size'], $size1);
    $this->assertEqual($media1['etag'], $etag1);
  }

  /*function testUpdate()
  {
    $this->db->sqlInsert('image_object', array('id' => $id = 1000,
                                               'description' => 'Description'));

    $this->db->sqlInsert('image_variation', array('id' => $variation_id = 1000,
                                                  'media_id' => $media_id = 101,
                                                  'image_id' => $id = 100,
                                                  'variation' => 'whatever'));

    $this->db->sqlInsert('media', array('id' => $media_id,
                                        'media_file_id' => $old_media_file_id = 'sdFjfskd23923sds',
                                        'file_name' => 'file1',
                                        'mime_type' => 'type1',
                                        'size' => 20,
                                        'etag' => 'etag1'));


    $image = new FileObject();
    $image->setId($id);
    $image->setDescription($description = 'some description');

    $image_variation1 = new ImageVariation();
    $image_variation1->setId($variation_id);
    $image_variation1->setMediaId($media_id);
    $image_variation1->setWidth($width1 = 50);
    $image_variation1->setHeight($height1 = 100);
    $image_variation1->setMediaFileId($media_file_id1 = 'dsada');//note it's a new one!!!
    $image_variation1->setName($name1 = 'original');
    $image_variation1->setEtag($etag1 = 'dsajadhk');
    $image_variation1->setMimeType($mime_type1 = 'jpeg');
    $image_variation1->setSize($size1 = 500);
    $image_variation1->setFileName($file_name1 = 'some file');

    $image->attachVariation($image_variation1);

    $this->media_manager->expectOnce('unlinkMedia', array($old_media_file_id));

    $image_variation2 = new ImageVariation();
    $image_variation2->setWidth($width2 = 100);
    $image_variation2->setHeight($height2 = 200);
    $image_variation2->setMediaFileId($media_file_id2 = 'dsfsdf');
    $image_variation2->setName($name2 = 'icon');
    $image_variation2->setEtag($etag2 = 'dsajrwek');
    $image_variation2->setMimeType($mime_type2 = 'png');
    $image_variation2->setSize($size2 = 500);
    $image_variation2->setFileName($file_name2 = 'some file2');

    $image->attachVariation($image_variation2);

    $this->mapper->expectOnce('_doParentUpdate', array($image));
    $this->mapper->update($image);

    $this->db->sqlSelect('media');
    $media_rows = $this->db->getArray();

    $this->assertEqual(sizeof($media_rows), 2);

    $media1 = reset($media_rows);
    $this->assertEqual($media1['id'], $media_id);
    $this->assertEqual($media1['media_file_id'], $media_file_id1);
    $this->assertEqual($media1['file_name'], $file_name1);
    $this->assertEqual($media1['mime_type'], $mime_type1);
    $this->assertEqual($media1['size'], $size1);
    $this->assertEqual($media1['etag'], $etag1);

    $media2 = next($media_rows);
    $this->assertEqual($media2['id'], $image_variation2->getMediaId());
    $this->assertEqual($media2['media_file_id'], $media_file_id2);
    $this->assertEqual($media2['file_name'], $file_name2);
    $this->assertEqual($media2['mime_type'], $mime_type2);
    $this->assertEqual($media2['size'], $size2);
    $this->assertEqual($media2['etag'], $etag2);

    $this->db->sqlSelect('image_variation');

    $variation_rows = $this->db->getArray();

    $this->assertEqual(sizeof($variation_rows), 2);

    $variation_data1 = reset($variation_rows);
    $this->assertEqual($variation_data1['image_id'], $image->getId());
    $this->assertEqual($variation_data1['media_id'], $media_id);
    $this->assertEqual($variation_data1['width'], $width1);
    $this->assertEqual($variation_data1['height'], $height1);
    $this->assertEqual($variation_data1['variation'], $name1);

    $variation_data2 = next($variation_rows);
    $this->assertEqual($variation_data2['image_id'], $image->getId());
    $this->assertEqual($variation_data2['media_id'], $image_variation2->getMediaId());
    $this->assertEqual($variation_data2['width'], $width2);
    $this->assertEqual($variation_data2['height'], $height2);
    $this->assertEqual($variation_data2['variation'], $name2);
  }*/
}

?>