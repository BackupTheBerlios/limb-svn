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
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/core/UnitOfWork.class.php');
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');
require_once(LIMB_DIR . '/core/data_mappers/AbstractDataMapper.class.php');
require_once(LIMB_DIR . '/core/Object.class.php');
require_once(WACT_ROOT . '/iterator/arraydataset.inc.php');

Mock :: generate('LimbToolkit');
Mock :: generate('AbstractDataMapper');
Mock :: generate('SQLBasedDAO');

class UOWTestObject extends Object
{
  var $__class_name = 'UOWTestObject';//php4 getclass workaround
}

class UnitOfWorkTest extends LimbTestCase
{
  var $uow;
  var $toolkit;

  function UnitOfWorkTest()
  {
    parent :: LimbTestCase('unit of work tests');
  }

  function setUp()
  {
    $this->uow = new UnitOfWork();
    $this->object = new UOWTestObject();
    $this->mapper = new MockAbstractDataMapper($this);
    $this->dao = new MockSQLBasedDAO($this);
    $this->toolkit = new MockLimbToolkit($this);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->mapper->tally();
    $this->dao->tally();
    $this->toolkit->tally();

    Limb :: popToolkit();
  }

  function testLoad()
  {
    $this->toolkit->setReturnReference('createObject', $this->object, array('UOWTestObject'));
    $this->toolkit->setReturnReference('createDAO', $this->dao, array('UOWTestObjectDAO'));
    $this->toolkit->setReturnReference('createDataMapper', $this->mapper, array('UOWTestObjectMapper'));

    $record = new DataSpace();
    $record->import($row = array('oid' => $id = 100, 'title' => $title = 'some title'));

    $this->dao->expectOnce('fetchById', array($id));
    $this->dao->setReturnValue('fetchById', $record, array($id));

    $this->mapper->expectOnce('load', array($record, $this->object));

    $object =& $this->uow->load('UOWTestObject', $id);

    $this->assertEqual($this->object, $object);
  }
}

?>
