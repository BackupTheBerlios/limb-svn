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
require_once(LIMB_DIR . '/class/db_tables/DbTableFactory.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbTable.class.php');

class TestImageDbTable extends DbTable
{
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


class TestImageVariationDbTable extends DbTable
{
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

class TestMediaDbTable extends DbTable
{
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
class DbTableCascadeDeleteTest extends LimbTestCase
{
  var $image = null;
  var $image_variation = null;
  var $media = null;

  var $dump_file = 'cascade_delete.sql';

  function setUp()
  {
    $this->image = DbTableFactory :: create('TestImage');
    $this->image_variation = DbTableFactory :: create('TestImageVariation');
    $this->media = DbTableFactory :: create('TestMedia');

    loadTestingDbDump(dirname(__FILE__) . '/../../sql/cascade_delete.sql');
  }

  function tearDown()
  {
    clearTestingDbTables();
  }

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

}
?>