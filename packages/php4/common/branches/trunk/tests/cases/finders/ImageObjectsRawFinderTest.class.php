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
require_once(dirname(__FILE__) . '/../../../finders/ImageObjectsRawFinder.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');

Mock :: generatePartial('ImageObjectsRawFinder',
                        'ImageObjectsRawFinderTestVersion',
                        array('_doParentFind'));

class ImageObjectsRawFinderTest extends LimbTestCase
{
  var $db;
  var $finder;

  function setUp()
  {
    $this->db =& DbFactory :: instance();

    $this->_cleanUp();

    $this->finder = new ImageObjectsRawFinderTestVersion($this);
  }

  function tearDown()
  {
    $this->_cleanUp();

    $this->finder->tally();
  }

  function _cleanUp()
  {
    $this->db->sqlDelete('image_variation');
    $this->db->sqlDelete('media');
  }

  function testFindEmpty()
  {
    $this->finder->setReturnValue('_doParentFind', array(), array(array(), array()));

    $this->assertTrue(!$this->finder->find());
  }

  function testFindWithVariations()
  {
    $this->db->sqlInsert('image_variation', array('id' => $id1 = 1000,
                                                   'media_id' => $media_id1 = 101,
                                                   'image_id' => $image_id1 = 10,
                                                   'variation' => $name1 = 'original',
                                                   'width' => $width1 = 20,
                                                   'height' => $height1 = 40));

    $this->db->sqlInsert('image_variation', array('id' => $id2 = 2000,
                                                   'media_id' => $media_id2 = 202,
                                                   'image_id' => $image_id2 = 20,
                                                   'variation' => $name2 = 'icon',
                                                   'width' => $width2 = 100,
                                                   'height' => $height2 = 200));

    $this->db->sqlInsert('image_variation', array('id' => $id3 = 3000,
                                                   'media_id' => $media_id3 = 303,
                                                   'image_id' => $image_id2,//note this
                                                   'variation' => $name3 = 'thumbnail',
                                                   'width' => $width3 = 130,
                                                   'height' => $height3 = 250));

    $this->db->sqlInsert('media', array('id' => $media_id1,
                                         'media_file_id' => $media_file_id1 = 'sdsda232dsds',
                                         'file_name' => $file_name1 = 'file1',
                                         'mime_type' => $mime_type1 = 'type1',
                                         'size' => $size1 = 20,
                                         'etag' => $etag1 = 'etag1'));

    $this->db->sqlInsert('media', array('id' => $media_id2,
                                         'media_file_id' => $media_file_id2 = 'sdsda252d5ds',
                                         'file_name' => $file_name2 = 'file2',
                                         'mime_type' => $mime_type2 = 'type2',
                                         'size' => $size2 = 30,
                                         'etag' => $etag2 = 'etag2'));

    $this->db->sqlInsert('media', array('id' => $media_id3,
                                         'media_file_id' => $media_file_id3 = 'sdsGKda252d5ds',
                                         'file_name' => $file_name3 = 'file3',
                                         'mime_type' => $mime_type3 = 'type3',
                                         'size' => $size3 = 340,
                                         'etag' => $etag3 = 'etag3'));


    $objects = array(array('object_id' => 10),
                     array('object_id' => 20));

    $params = 'some params';
    $sql_params = 'some sql params';

    $this->finder->expectOnce('_doParentFind', array($params, $sql_params));
    $this->finder->setReturnValue('_doParentFind', $objects, array($params, $sql_params));

    $result = array(array('object_id' => 10,
                          'variations' => array('original' => array('id' => $id1,
                                                                    'media_id' => $media_id1,
                                                                    'media_file_id' => $media_file_id1,
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
                                                                'media_file_id' => $media_file_id2,
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
                                                                'media_file_id' => $media_file_id3,
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