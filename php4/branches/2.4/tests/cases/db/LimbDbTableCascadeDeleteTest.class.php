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
require_once(LIMB_DIR . '/core/db_tables/LimbDbTableFactory.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbTable.class.php');

class TestCascadeMasterDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'test_cascade_master';
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
            'table_name' => 'test_cascade_slave',
            'field' => 'image_id',
          ),
      ),
    );
  }
}


class TestCascadeSlaveDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'test_cascade_slave';
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
            'table_name' => 'test_cascade_other',
            'field' => 'id',
          ),
      ),
    );
  }
}

class TestCascadeOtherDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'test_cascade_other';
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

  function LimbDbTableCascadeDeleteTest()
  {
    parent :: LimbTestCase('cascade delete db table tests');
  }

  function setUp()
  {
    $this->master = LimbDbTableFactory :: create('TestCascadeMaster');
    $this->slave = LimbDbTableFactory :: create('TestCascadeSlave');
    $this->other = LimbDbTableFactory :: create('TestCascadeOther');

    loadTestingDbDump(dirname(__FILE__) . '/../../sql/cascade_delete.sql');
  }

  function tearDown()
  {
    clearTestingDbTables();
  }

  function testCascadeDelete()
  {
    $this->slave->delete(array('id' => 16));

    $medias =& $this->other->select();
    $this->assertEqual($medias->getTotalRowCount(), 11);

    $variations =& $this->slave->select();
    $this->assertEqual($variations->getTotalRowCount(), 11);
  }

  function testNestedCascadeDelete()
  {
    $this->master->delete(array('id' => 12));

    $images =& $this->master->select();
    $this->assertEqual($images->getTotalRowCount(), 4);

    $variations =& $this->slave->select();
    $this->assertEqual($variations->getTotalRowCount(), 9);

    $medias =& $this->other->select();
    $this->assertEqual($medias->getTotalRowCount(), 9);
  }

  /*
  function testCascadeDelete()
  {
    $this->slave->delete(array('id' => 16));

    $this->assertEqual(sizeof($this->slave->getList()), 11);
    $this->assertEqual(sizeof($this->other->getList()), 11);
  }

  function testNestedCascadeDelete()
  {
    $this->master->delete(array('id' => 12));

    $this->assertEqual(sizeof($this->master->getList()), 4);
    $this->assertEqual(sizeof($this->slave->getList()), 9);
    $this->assertEqual(sizeof($this->other->getList()), 9);
  }
  */

}
?>