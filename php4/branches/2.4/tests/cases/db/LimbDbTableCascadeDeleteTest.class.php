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
require_once(LIMB_DIR . '/class/db_tables/LimbDbTableFactory.class.php');
require_once(LIMB_DIR . '/class/db/LimbDbTable.class.php');

class TestImageDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'test_image';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'description' => '',
      'title' => '',
    );
  }

  function _defineConstraints()
  {
    return array(
      'id' =>	array(
          0 => array(
            'table_name' => 'test_image_variation',
            'field' => 'image_id',
          ),
      ),
    );
  }
}


class TestImageVariationDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'test_image_variation';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'image_id' => array('type' => 'numeric'),
      'media_id' => '',
      'width' => '',
      'height' => '',
      'variation' => ''
    );
  }

  function _defineConstraints()
  {
    return array(
      'media_id' =>	array(
          0 => array(
            'table_name' => 'test_media',
            'field' => 'id',
          ),
      ),
    );
  }
}

class TestMediaDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'test_media';
  }

  function _defineColumns()
  {
    return array(
      'id' => '',
      'file_name' => '',
      'mime_type' => '',
      'size' => '',
      'etag' => '',
    );
  }
}
class LimbDbTableCascadeDeleteTest extends LimbTestCase
{
  var $image = null;
  var $image_variation = null;
  var $media = null;

  var $dump_file = 'cascade_delete.sql';

  function LimbDbTableCascadeDeleteTest()
  {
    parent :: LimbTestCase('cascade delete db table tests');
  }

  function setUp()
  {
    $this->image = LimbDbTableFactory :: create('TestImage');
    $this->image_variation = LimbDbTableFactory :: create('TestImageVariation');
    $this->media = LimbDbTableFactory :: create('TestMedia');

    loadTestingDbDump(dirname(__FILE__) . '/../../sql/cascade_delete.sql');
  }

  function tearDown()
  {
    clearTestingDbTables();
  }

  function testCascadeDelete()
  {
    $this->image_variation->delete(array('id' => 16));

    $medias =& $this->media->select();
    $this->assertEqual($medias->getTotalRowCount(), 11);

    $variations =& $this->image_variation->select();
    $this->assertEqual($variations->getTotalRowCount(), 11);
  }

  function testNestedCascadeDelete()
  {
    $this->image->delete(array('id' => 12));

    $images =& $this->image->select();
    $this->assertEqual($images->getTotalRowCount(), 4);

    $variations =& $this->image_variation->select();
    $this->assertEqual($variations->getTotalRowCount(), 9);

    $medias =& $this->media->select();
    $this->assertEqual($medias->getTotalRowCount(), 9);
  }

  /*
  function testCascadeDelete()
  {
    $this->image_variation->delete(array('id' => 16));

    $this->assertEqual(sizeof($this->image_variation->getList()), 11);
    $this->assertEqual(sizeof($this->media->getList()), 11);
  }

  function testNestedCascadeDelete()
  {
    $this->image->delete(array('id' => 12));

    $this->assertEqual(sizeof($this->image->getList()), 4);
    $this->assertEqual(sizeof($this->image_variation->getList()), 9);
    $this->assertEqual(sizeof($this->media->getList()), 9);
  }
  */

}
?>