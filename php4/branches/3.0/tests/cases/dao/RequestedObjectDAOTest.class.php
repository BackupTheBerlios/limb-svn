<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: TreeBranchCriteriaTest.class.php 1173 2005-03-17 11:36:43Z seregalimb $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/UnitOfWork.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');
require_once(LIMB_DIR . '/core/DAO/RequestedObjectDAO.class.php');
require_once(LIMB_DIR . '/core/DAO/SQLBasedDAO.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                        'LimbBaseToolkitRequestedObjectDAOTestVersion',
                        array('getRequest',
                              'getUOW',
                              'createDAO'));

Mock :: generate('UnitOfWork');
Mock :: generate('Request');
Mock :: generate('SQLBasedDAO');

class RequestedObjectDAOTest extends LimbTestCase
{
  var $dao;
  var $toolkit;
  var $uow;
  var $request;
  var $conn;
  var $db;

  function RequestedObjectDAOTest()
  {
    parent :: LimbTestCase('requested object dao tests');
  }

  function setUp()
  {
    $toolkit = Limb :: toolkit();
    $this->conn =& $toolkit->getDBConnection();
    $this->db = new SimpleDB($this->conn);

    $this->uow = new MockUnitOfWork($this);
    $this->request = new MockRequest($this);
    $this->dao = new MockSQLBasedDAO($this);

    $this->toolkit = new LimbBaseToolkitRequestedObjectDAOTestVersion($this);
    $this->toolkit->setReturnReference('getRequest', $this->request);
    $this->toolkit->setReturnReference('getUOW', $this->uow);
    $this->toolkit->setReturnReference('createDAO', $this->dao, array('ObjectsClassNamesDAO'));

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    Limb :: restoreToolkit();

    $this->toolkit->tally();
    $this->uow->tally();
    $this->request->tally();
    $this->dao->tally();
  }

  function testFetch()
  {
    $dataspace = new Dataspace();
    $dataspace->set('name', $class_name = 'TestArticle');

    $this->dao->expectOnce('fetchById', array($id = 10));
    $this->dao->setReturnReference('fetchById', $dataspace);

    $this->request->expectOnce('get', array('id'));
    $this->request->setReturnValue('get', $id, array('id'));

    $object = new Dataspace();
    $object->set('id', $id);

    $this->uow->expectOnce('load', array($class_name, $id));
    $this->uow->setReturnReference('load', $object, array($class_name, $id));

    $dao = new RequestedObjectDAO();
    $this->assertEqual($dao->fetch(), $object);
  }
}
?>
