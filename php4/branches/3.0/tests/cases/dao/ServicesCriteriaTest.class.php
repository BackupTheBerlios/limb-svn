<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: OneTableObjectsCriteriaTest.class.php 1085 2005-02-02 16:04:20Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');
require_once(LIMB_DIR . '/core/dao/criteria/ServicesCriteria.class.php');

class ServicesCriteriaTest extends LimbTestCase
{
  var $dao;
  var $db;
  var $conn;

  function ServicesCriteriaTest()
  {
    parent :: LimbTestCase('services criteria tests');
  }

  function setUp()
  {
    $this->conn =& LimbDbPool :: getConnection();
    $this->db =& new SimpleDb($this->conn);

    $this->_cleanUp();

    $this->dao = new SQLBasedDAO();
    $sql = new ComplexSelectSQL('SELECT sys_object.oid as oid %fields% FROM sys_object %tables% %where%');
    $this->dao->setSQL($sql);

    $this->dao->addCriteria(new ServicesCriteria());

    $this->_insertObjectRecords();
    $this->_insertServicesRecords();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_service');
    $this->db->delete('sys_object');
  }

  function testCorrectLink()
  {
    $sql =& $this->dao->getSQL();
    $sql->addCondition('sys_object.oid = 3');

    $rs =& new SimpleDbDataset($this->dao->fetch());
    $record = $rs->getRow();

    // see _insertServicesRecords for details
    $this->assertEqual($record['service_id'], 103);
    $this->assertEqual($record['oid'], 3);
    $this->assertEqual($record['title'], 'service_3_title');
    $this->assertEqual($record['behaviour_id'], 503);
  }

  function _insertObjectRecords()
  {
    $toolkit =& Limb :: toolkit();
    $table =& $toolkit->createDBTable('SysObject');

    for($i = 1; $i <= 5; $i++)
    {
      $values['oid'] = $i;
      $values['class_id'] = 150;
      $table->insert($values);
    }
  }

  function _insertServicesRecords()
  {
    $data = array();
    for($i = 1; $i <= 5; $i++)
    {
      $this->db->insert('sys_service',
        array(
          'service_id' => $i+100,
          'oid' => $i,
          'title' => 'service_' . $i . '_title',
          'behaviour_id' => $i+500,
        )
      );
    }
  }

}
?>
