<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: ImageObjectsRawFinderTest.class.php 1091 2005-02-03 13:10:12Z pachanga $
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/../../../dao/ImageObjectsDAO.class.php');
require_once(dirname(__FILE__) . '/../../../dao/ImageObjectsRecordSet.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');
require_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');

class ImageObjectsRecordSetTest extends LimbTestCase
{
  var $db;

  function ImageObjectsRecordSetTest()
  {
    parent :: LimbTestCase('image objects record set test');
  }

  function setUp()
  {
    $this->db =& new SimpleDb(LimbDbPool :: getConnection());

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('image_variation');
    $this->db->delete('media');
  }

  function testEmpty()
  {
    $rs = new ImageObjectsRecordSet(new PagedArrayDataset(array()));
    $rs->rewind();
    $this->assertFalse($rs->valid());
  }

  function testFindWithVariations()
  {
    $this->db->insert('image_variation', array('id' => $id1 = 1000,
                                                   'media_id' => $media_id1 = 101,
                                                   'image_id' => $image_id1 = 10,
                                                   'variation' => $name1 = 'original',
                                                   'width' => $width1 = 20,
                                                   'height' => $height1 = 40));

    $this->db->insert('image_variation', array('id' => $id2 = 2000,
                                                   'media_id' => $media_id2 = 202,
                                                   'image_id' => $image_id2 = 20,
                                                   'variation' => $name2 = 'icon',
                                                   'width' => $width2 = 100,
                                                   'height' => $height2 = 200));

    $this->db->insert('image_variation', array('id' => $id3 = 3000,
                                                   'media_id' => $media_id3 = 303,
                                                   'image_id' => $image_id2,//note this
                                                   'variation' => $name3 = 'thumbnail',
                                                   'width' => $width3 = 130,
                                                   'height' => $height3 = 250));

    $this->db->insert('media', array('id' => $media_id1,
                                         'media_file_id' => $media_file_id1 = 'sdsda232dsds',
                                         'file_name' => $file_name1 = 'file1',
                                         'mime_type' => $mime_type1 = 'type1',
                                         'size' => $size1 = 20,
                                         'etag' => $etag1 = 'etag1'));

    $this->db->insert('media', array('id' => $media_id2,
                                         'media_file_id' => $media_file_id2 = 'sdsda252d5ds',
                                         'file_name' => $file_name2 = 'file2',
                                         'mime_type' => $mime_type2 = 'type2',
                                         'size' => $size2 = 30,
                                         'etag' => $etag2 = 'etag2'));

    $this->db->insert('media', array('id' => $media_id3,
                                         'media_file_id' => $media_file_id3 = 'sdsGKda252d5ds',
                                         'file_name' => $file_name3 = 'file3',
                                         'mime_type' => $mime_type3 = 'type3',
                                         'size' => $size3 = 340,
                                         'etag' => $etag3 = 'etag3'));


    $objects = array(array('id' => 10),
                     array('id' => 20));


    $rs = new ImageObjectsRecordSet(new PagedArrayDataSet($objects));
    $this->assertEqual($rs->getRowCount(), 2);
    $this->assertEqual($rs->getTotalRowCount(), 2);

    $rs->rewind();

    $next1 = array('id' => 10,
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
                                                            'etag' => $etag1)));
    $record = $rs->current();
    $this->assertEqual($record->export(), $next1);

    $next2 = array('id' => 20,
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
                                                        'etag' => $etag3)));

    $rs->next();
    $record = $rs->current();
    $this->assertEqual($record->export(), $next2);

    $rs->next();
    $this->assertFalse($rs->valid());
  }
}

?>