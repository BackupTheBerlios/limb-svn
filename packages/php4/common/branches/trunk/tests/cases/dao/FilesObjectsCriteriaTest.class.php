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
require_once(LIMB_DIR . '/tests/cases/dao/SiteObjectsSQLBaseTest.class.php');
require_once(LIMB_DIR . '/core/dao/SiteObjectsDAO.class.php');
require_once(LIMB_DIR . '/core/db/ComplexSelectSQL.class.php');

Mock :: generatePartial('FileObjectsRawFinder',
                        'FileObjectsRawFinderTestVersion',
                        array('_doParentFind'));

class FileObjectsCriteriaTest extends LimbTestCase
{
  var $db;
  var $conn;

  function FileObjectsCriteriaTest()
  {
    parent :: LimbTestCase('file objects criteria tests');
  }

  function setUp()
  {
    $this->conn =& LimbDbPool :: getConnection();
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
  }

  function testCorrectLink()
  {
    $this->db->insert('media', array('id' => $media_id1 = 10,
                                     'media_file_id' => $media_file_id1 = 'sdsda232dsds',
                                     'file_name' => $file_name1 = 'file1',
                                     'mime_type' => $mime_type1 = 'type1',
                                     'size' => $size1 = 20,
                                     'etag' => $etag1 = 'etag1'));

    $this->db->insert('file_object', array('id' => $file_id1 = 1,
                                    'media_id' => $media_id1));

    $dao = new SiteObjectsDAO();
    $dao->addCriteria(new FileObjectsCriteria());

    $sql =& new ComplexSelectSQL("SELECT %fields% FROM file_object as tn %tables% %where% %group% %order%");
    $sql->addField('tn.id as id');
    $sql->addField('tn.media_id as media_id');

    $dao->setSQL($sql);

    $rs =& new SimpleDbDataset($dao->fetch());
    $record = $rs->getRow();

    $this->assertEqual($record['id'], $file_id1);
    $this->assertEqual($record['media_id'], $media_id1);
    $this->assertEqual($record['file_name'], $file_name1);
    $this->assertEqual($record['media_file_id'], $media_file_id1);
    $this->assertEqual($record['mime_type'], $mime_type1);
    $this->assertEqual($record['etag'], $etag1);
    $this->assertEqual($record['size'], $size1);
  }
}

?>