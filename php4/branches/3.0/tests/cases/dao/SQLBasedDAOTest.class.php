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
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');
require_once(LIMB_DIR . '/core/dao/criteria/Criteria.class.php');
require_once(LIMB_DIR . '/core/db/ComplexSelectSQL.class.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');
require_once(WACT_ROOT . '/db/drivers/mysql/driver.inc.php');
require_once(WACT_ROOT . '/iterator/arraydataset.inc.php');

Mock :: generate('Criteria');
Mock :: generate('ComplexSelectSQL');
Mock :: generate('LimbToolkit');
Mock :: generate('MySQLConnection');
Mock :: generate('MySqlQueryStatement');
Mock :: generatePartial('SQLBasedDAO', 'SQLBasedDAOTestVersion',
                 array('_initSQL'));

class SQLBasedDAOTest extends LimbTestCase
{
  var $dao;
  var $sql;
  var $conn;
  var $stmt;

  function SQLBasedDAOTest()
  {
    parent :: LimbTestCase('SQLBasedDAO test');
  }

  function setUp()
  {
    $this->sql = new MockComplexSelectSQL($this);
    $this->conn = new MockMySQLConnection($this);
    $this->stmt = new MockMySqlQueryStatement($this);

    $toolkit = new MockLimbToolkit($this);
    $toolkit->setReturnReference('getDbConnection', $this->conn);

    $this->dao = new SQLBasedDAOTestVersion($this);
    $this->dao->setReturnReference('_initSQL', $this->sql);

    $this->dao->SQLBasedDAO();

    Limb :: registerToolkit($toolkit);
  }

  function tearDown()
  {
    $this->dao->tally();
    $this->sql->tally();
    $this->conn->tally();
    $this->stmt->tally();

    Limb :: popToolkit();
  }

  function testProcess()
  {
    $criteria1 = new MockCriteria($this);
    $criteria2 = new MockCriteria($this);

    $this->dao->addCriteria($criteria1);
    $this->dao->addCriteria($criteria2);

    $criteria1->expectOnce('process', array(new IsAExpectation('MockComplexSelectSQL')));
    $criteria2->expectOnce('process', array(new IsAExpectation('MockComplexSelectSQL')));

    $this->sql->expectOnce('toString');
    $this->sql->setReturnValue('toString', $str = 'SELECT *...');

    $this->conn->expectOnce('newStatement', array($str));
    $this->conn->setReturnReference('newStatement', $this->stmt, array($str));

    $this->stmt->expectOnce('getRecordSet');
    $this->stmt->setReturnValue('getRecordSet', $rs = 'anything');

    $this->assertEqual($this->dao->fetch(), $rs);
  }

  function testFindById()
  {
    $id = "10";

    $this->sql->expectOnce('addCondition', array('oid=10'));
    $this->sql->expectOnce('toString');
    $this->sql->setReturnValue('toString', $str = 'SELECT *...');

    $this->conn->expectOnce('newStatement', array($str));
    $this->conn->setReturnReference('newStatement', $this->stmt, array($str));

    $record = new DataSpace();
    $record->import($row = array('whatever'));

    $this->stmt->expectOnce('getRecordSet');
    $this->stmt->setReturnValue('getRecordSet', $rs = new ArrayDataSet(array($row)));

    $this->assertEqual($this->dao->fetchById($id), $record);
  }
}

?>