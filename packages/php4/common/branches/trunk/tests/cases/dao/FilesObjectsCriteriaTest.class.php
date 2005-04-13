<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: FileObjectsRawFinderTest.class.php 1091 2005-02-03 13:10:12Z pachanga $
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/../../../dao/criteria/FileObjectsCriteria.class.php');
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');
require_once(LIMB_DIR . '/core/db/ComplexSelectSQL.class.php');

class FileObjectsCriteriaTest extends LimbTestCase
{
  var $db;
  var $conn;

  function FileObjectsCriteriaTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $this->conn =& $toolkit->getDbConnection();
    $this->db =& new SimpleDb($this->conn);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('file_object');
    $this->db->delete('media');
    $this->db->delete('sys_object');
  }

  function testCorrectLink()
  {
    $this->db->insert('media', array('id' => $media_id1 = 10,
                                     'media_file_id' => $media_file_id1 = 'sdsda232dsds',
                                     'file_name' => $file_name1 = 'file1',
                                     'mime_type' => $mime_type1 = 'type1',
                                     'size' => $size1 = 20,
                                     'etag' => $etag1 = 'etag1'));

    $this->db->insert('file_object', array('oid' => $file_id1 = 1,
                                    'media_id' => $media_id1));

    $this->db->insert('sys_object', array('oid' => $file_id1,
                                    'class_id' => 1000));

    $dao = new SQLBasedDAO();

    $sql =& new ComplexSelectSQL("SELECT sys_object.oid %fields% FROM sys_object %tables% %where% %group% %order%");

    $dao->setSQL($sql);
    $dao->addCriteria(new FileObjectsCriteria());

    $rs =& new SimpleDbDataset($dao->fetch());
    $record = $rs->getRow();

    $this->assertEqual($record['oid'], $file_id1);
    $this->assertEqual($record['media_id'], $media_id1);
    $this->assertEqual($record['file_name'], $file_name1);
    $this->assertEqual($record['media_file_id'], $media_file_id1);
    $this->assertEqual($record['mime_type'], $mime_type1);
    $this->assertEqual($record['etag'], $etag1);
    $this->assertEqual($record['size'], $size1);
  }
}

?>