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
require_once(LIMB_DIR . '/core/dao/DAO.class.php');
require_once(LIMB_DIR . '/core/dao/criteria/Criteria.class.php');
require_once(LIMB_DIR . '/core/db/ComplexSelectSQL.class.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');
require_once(WACT_ROOT . '/db/drivers/mysql/driver.inc.php');

Mock :: generate('Criteria');
Mock :: generate('ComplexSelectSQL');
Mock :: generate('LimbToolkit');
Mock :: generate('MySQLConnection');
Mock :: generate('MySqlQueryStatement');
Mock :: generatePartial('DAO', 'DAOTestVersion',
                 array('_initSQL'));

class DAOTest extends LimbTestCase
{
  var $dao;
  var $sql;
  var $conn;
  var $stmt;

  function DAOTest()
  {
    parent :: LimbTestCase('DAO test');
  }

  function setUp()
  {
    $this->sql = new MockComplexSelectSQL($this);
    $this->conn = new MockMySQLConnection($this);
    $this->stmt = new MockMySqlQueryStatement($this);

    $toolkit = new MockLimbToolkit($this);
    $toolkit->setReturnReference('getDbConnection', $this->conn);

    $this->dao = new DAOTestVersion($this);
    $this->dao->setReturnReference('_initSQL', $this->sql);

    $this->dao->DAO();

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
    $this->sql->setReturnValue('toString', $str = 'SELECT * FROM test1');

    $this->conn->expectOnce('newStatement', array($str));
    $this->conn->setReturnReference('newStatement', $this->stmt, array($str));

    $this->stmt->expectOnce('getRecordSet');
    $this->stmt->setReturnValue('getRecordSet', $rs = 'anything');

    $this->assertEqual($this->dao->fetch(), $rs);
  }
}

?>