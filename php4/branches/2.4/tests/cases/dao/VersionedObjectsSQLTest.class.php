<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: OneTableObjectsSQLTest.class.php 1068 2005-01-28 14:01:40Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/datasources/OneTableObjectsSQL.class.php');
require_once(LIMB_DIR . '/core/datasources/VersionedOneTableObjectsSQL.class.php');
require_once(dirname(__FILE__) . '/SiteObjectsSQLBaseTest.class.php');
require_once(dirname(__FILE__) . '/DocumentTestDBTable.class.php');

class VersionedOneTableObjectsSQLTest extends SiteObjectsSQLBaseTest
{
  function VersionedOneTableObjectsSQLTest()
  {
    parent :: SiteObjectsSQLBaseTest('versioned objects sql tests');
  }

  function setUp()
  {
    parent :: setUp();

    $this->sql =& new VersionedOneTableObjectsSQL(new OneTableObjectsSQL(new SiteObjectsRawSQL(), 'DocumentTest'));

    $this->_insertExtraTableRecords();

    $this->_insertFakeExtraTableRecords();
  }

  function _cleanUp()
  {
    parent :: _cleanUp();

    $this->db->delete('document');
  }

  function testCorrectLink()
  {
    $stmt =& $this->conn->newStatement($this->sql->toString());
    $rs =& new SimpleDbDataset($stmt->getRecordSet());

    $this->assertEqual($rs->getTotalRowCount(), 5);

    $record = $rs->getRow();

    $this->assertEqual($record['identifier'], 'object_1');
    $this->assertEqual($record['title'], 'object_1_title');
    $this->assertEqual($record['content'], 'object_1_content');
    $this->assertEqual($record['version'], 1);
    $this->assertEqual($record['annotation'], 'object_1_annotation');
  }

  function _insertExtraTableRecords()
  {
    $data = array();
    for($i = 1; $i <= 5; $i++)
    {
      $this->db->insert('document',
        array(
          'id' => $i+100,
          'object_id' => $i,
          'version' => 1,
          'identifier' => 'object_' . $i,
          'title' => 'object_' . $i . '_title',
          'content' => 'object_' . $i . '_content',
          'annotation' => 'object_' . $i . '_annotation',
        )
      );
    }
  }

  function _insertFakeExtraTableRecords()
  {
    $data = array();
    for($i = 1; $i <= 5; $i++)
    {
      $version = mt_rand(2, 5);
      $this->db->insert('document',
        array(
          'id' => $i+105,
          'object_id' => $i,
          'version' => $version,
          'identifier' => 'object_' . $i,
          'title' => 'object_' . $i . '_title',
          'content' => 'object_' . $i . '_content',
          'annotation' => 'object_' . $i . '_annotation',
        )
      );
    }
  }

}
?>
