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
require_once(LIMB_DIR . '/core/DAO/SQLBasedDAO.class.php');
require_once(LIMB_DIR . '/core/DAO/criteria/ServicesCriteria.class.php');

Mock :: generatePartial('SQLBasedDAO',
                        'SQLBasedDAOSCTestVersion',
                        array('_initSQL'));

class ServicesCriteriaTest extends LimbTestCase
{
  var $dao;
  var $sql;
  var $db;
  var $conn;

  function ServicesCriteriaTest()
  {
    parent :: LimbTestCase('services criteria tests');
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $this->conn =& $toolkit->getDbConnection();
    $this->db =& new SimpleDb($this->conn);

    $this->_cleanUp();

    $this->dao= new SQLBasedDAOSCTestVersion($this);

    $this->sql = new ComplexSelectSQL('SELECT sys_object.oid as oid %fields% FROM sys_object %tables% %where%');
    $this->dao->setReturnReference('_initSQL', $this->sql);

    $this->dao->SQLBasedDAO();

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
    $this->sql->addCondition('sys_object.oid = 3');

    $rs =& new SimpleDbDataset($this->dao->fetch());
    $record = $rs->getRow();

    // see _insertServicesRecords for details
    $this->assertEqual($record['service_id'], 103);
    $this->assertEqual($record['oid'], 3);
    $this->assertEqual($record['title'], 'service_3_title');
    $this->assertEqual($record['service_id'], 503);
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
          'service_id' => $i+500,
        )
      );
    }
  }

}
?>
