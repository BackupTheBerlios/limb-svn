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
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/UnitOfWork.class.php');
require_once(LIMB_DIR . '/core/cache/CachePersisterKeyDecorator.class.php');
require_once(LIMB_DIR . '/core/cache/CacheMemoryPersister.class.php');
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');
require_once(LIMB_DIR . '/core/data_mappers/AbstractDataMapper.class.php');
require_once(LIMB_DIR . '/core/Object.class.php');
require_once(WACT_ROOT . '/iterator/arraydataset.inc.php');

Mock :: generate('LimbBaseToolkit', 'MockLimbToolkit');
Mock :: generate('AbstractDataMapper');
Mock :: generate('SQLBasedDAO');

class UOWTestObject extends Object
{
  var $__class_name = 'UOWTestObject';//php4 getclass workaround
}

class UOWTestObjectMapper extends MockAbstractDataMapper//required for UOWTestObject!
{
  function UOWTestObjectMapper(&$test)
  {
    parent :: MockAbstractDataMapper($test);
  }
}

class UnitOfWorkTest extends LimbTestCase
{
  var $uow;
  var $toolkit;
  var $mapper;
  var $dao;

  function UnitOfWorkTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->toolkit = new MockLimbToolkit($this);

    Limb :: registerToolkit($this->toolkit);

    $this->mapper = new UOWTestObjectMapper($this);
    $this->dao = new MockSQLBasedDAO($this);
    $this->cache = new CachePersisterKeyDecorator(new CacheMemoryPersister());

    $this->mapper->setReturnValue('getIdentityKeyName', 'id');

    $this->toolkit->setReturnReference('getCache', $this->cache);
    $this->toolkit->setReturnReference('createDAO', $this->dao, array('UOWTestObjectDAO'));
    $this->toolkit->setReturnReference('createDataMapper', $this->mapper, array('UOWTestObjectMapper'));

    $this->uow = new UnitOfWork();
  }

  function tearDown()
  {
    $this->mapper->tally();
    $this->dao->tally();
    $this->toolkit->tally();

    Limb :: restoreToolkit();
  }

  function testRegisterNew()
  {
    $obj = new UOWTestObject();

    $this->toolkit->expectOnce('nextUID');
    $this->toolkit->setReturnValue('nextUID', $new_id = 10);

    $this->uow->registerNew($obj);

    $this->assertEqual($obj->get('id'), $new_id);

    $this->assertTrue($this->uow->isRegistered($obj));
    $this->assertTrue($this->uow->isNew($obj));
    $this->assertFalse($this->uow->isExisting($obj));
  }

  function testRegisterExisting()
  {
    $obj = new UOWTestObject();

    $this->toolkit->expectNever('nextUID');

    $obj->set('id', 10);
    $this->uow->registerExisting($obj);

    $this->assertTrue($this->uow->isRegistered($obj));
    $this->assertFalse($this->uow->isNew($obj));
    $this->assertTrue($this->uow->isExisting($obj));
  }

  function testRegisterNewObjectAsExisting()
  {
    $obj = new UOWTestObject();

    $this->toolkit->expectOnce('nextUID');
    $this->toolkit->setReturnValue('nextUID', $new_id = 10);

    $this->uow->registerNew($obj);

    $this->assertEqual($obj->get('id'), $new_id);

    $this->assertTrue($this->uow->isRegistered($obj));
    $this->assertTrue($this->uow->isNew($obj));
    $this->assertFalse($this->uow->isExisting($obj));

    $this->uow->registerExisting($obj);

    $this->assertTrue($this->uow->isRegistered($obj));
    $this->assertFalse($this->uow->isNew($obj));
    $this->assertTrue($this->uow->isExisting($obj));
  }

  function testIsDirty()
  {
    $obj = new UOWTestObject();
    $obj->set('id', 13);

    $this->uow->registerExisting($obj);

    $this->assertFalse($this->uow->isDirty($obj));

    $obj->set('foo', 1);

    $this->assertTrue($this->uow->isDirty($obj));
  }

  function testDeleteNewObject()
  {
    $obj = new UOWTestObject();
    $this->toolkit->setReturnValue('nextUID', $id = 10);

    $this->uow->registerNew($obj);

    $this->assertFalse($this->uow->isDeleted($obj));

    $this->uow->delete($obj);

    $this->assertFalse($this->uow->isDeleted($obj));
    $this->assertFalse($this->uow->isRegistered($obj));
  }

  function testDeleteExistingObject()
  {
    $obj = new UOWTestObject();
    $obj->set('id', 10);

    $this->uow->registerExisting($obj);

    $this->assertFalse($this->uow->isDeleted($obj));

    $this->uow->delete($obj);

    $this->assertTrue($this->uow->isDeleted($obj));
    $this->assertTrue($this->uow->isRegistered($obj));
  }

  function testReset()
  {
    $obj1 = new UOWTestObject();
    $obj2 = new UOWTestObject();
    $obj2->set('id', 20);

    $this->toolkit->setReturnValue('nextUID', 10);

    $this->uow->registerNew($obj1);
    $this->uow->registerExisting($obj2);
    $this->uow->delete($obj2);

    $this->uow->reset();

    $this->assertFalse($this->uow->isRegistered($obj1));
    $this->assertFalse($this->uow->isRegistered($obj2));
    $this->assertFalse($this->uow->isDeleted($obj2));
  }

  function testLoadNotFound()
  {
    $object = new UOWTestObject();

    $this->toolkit->setReturnReference('createObject', $object, array('UOWTestObject'));

    $this->dao->expectOnce('fetchById', array($id = 1));
    $this->dao->setReturnValue('fetchById', null, array($id));

    $this->mapper->expectNever('load');

    $this->assertNull($this->uow->load('UOWTestObject', $id));
    $this->assertFalse($this->uow->isRegistered($object));
  }

  function testLoad()
  {
    $record = new DataSpace();
    $record->import($row = array('whatever'));

    $object = new UOWTestObject();
    $object->set('id', $id = 100);//we do the mapper's job

    $this->toolkit->setReturnReference('createObject', $object, array('UOWTestObject'));

    $this->dao->expectOnce('fetchById', array($id));
    $this->dao->setReturnValue('fetchById', $record, array($id));

    $this->mapper->expectOnce('load', array($record, $object));

    $loaded_object =& $this->uow->load('UOWTestObject', $id);

    $this->assertTrue($this->uow->isExisting($loaded_object));
    $this->assertFalse($this->uow->isNew($loaded_object));

    $this->assertEqual($object, $loaded_object);

    //cache hit test
    $object =& $this->uow->load('UOWTestObject', $id);

    $this->assertEqual($object, $loaded_object);
  }

  function testCommitNewObjects()
  {
    $obj1 = new UOWTestObject();
    $obj2 = new UOWTestObject();

    $this->toolkit->expectCallCount('nextUID', 2);
    $this->toolkit->setReturnValueAt(0, 'nextUID', 10);
    $this->toolkit->setReturnValueAt(1, 'nextUID', 20);

    $this->uow->registerNew($obj1);
    $this->uow->registerNew($obj2);

    $this->mapper->expectCallCount('save', 2);
    $this->mapper->expectArgumentsAt(0, 'save', array($obj1));
    $this->mapper->expectArgumentsAt(1, 'save', array($obj2));

    $this->assertTrue($this->uow->isNew($obj1));
    $this->assertTrue($this->uow->isNew($obj2));

    $this->assertFalse($this->uow->isExisting($obj1));
    $this->assertFalse($this->uow->isExisting($obj2));

    $this->uow->commit();

    $this->assertFalse($this->uow->isNew($obj1));
    $this->assertFalse($this->uow->isNew($obj2));

    $this->assertTrue($this->uow->isExisting($obj1));
    $this->assertTrue($this->uow->isExisting($obj2));
  }

  function testCommitOnlyDirty()
  {
    $obj1 = new UOWTestObject();
    $obj1->set('id', $id1 = 1);

    $obj2 = new UOWTestObject();
    $obj2->set('id', $id2 = 2);

    $this->uow->registerExisting($obj1);
    $this->uow->registerExisting($obj2);

    $obj1->set('changed', true);

    $this->mapper->expectOnce('save', array($obj1));

    $this->uow->commit();
  }

  function testDeleteNew()
  {
    $obj = new UOWTestObject();

    die_on_error(false);
    $this->uow->delete($obj);
    die_on_error();

    $this->assertTrue(catch_error('LimbException', $e));

    $this->mapper->expectNever('delete');

    $this->uow->commit();
  }

  function testDelete()
  {
    $obj = new UOWTestObject();
    $obj->set('id', $id = 1);
    $this->uow->registerExisting($obj);

    $this->uow->delete($obj);

    $this->assertTrue($this->uow->isDeleted($obj));
    $this->assertTrue($this->uow->isExisting($obj));

    $this->mapper->expectOnce('delete', array($obj));

    $this->uow->commit();

    $this->assertFalse($this->uow->isDeleted($obj));
    $this->assertFalse($this->uow->isExisting($obj));

    $this->uow->commit();//deleted only once
  }

  function testEvictNewObject()
  {
    $obj = new UOWTestObject();
    $this->toolkit->setReturnValue('nextUID', 101);

    $this->uow->registerNew($obj);

    $this->assertTrue($this->uow->isRegistered($obj));

    $this->uow->evict($obj);

    $this->assertFalse($this->uow->isRegistered($obj));
  }

  function testEvictExistingObject()
  {
    $obj = new UOWTestObject();
    $obj->set('id', $id = 1);
    $this->uow->registerExisting($obj);

    $this->assertTrue($this->uow->isRegistered($obj));

    $this->uow->evict($obj);

    $this->assertFalse($this->uow->isRegistered($obj));
  }

  function testEvictToBeDeletedObject()
  {
    $obj = new UOWTestObject();
    $obj->set('id', $id = 1);

    $this->uow->registerExisting($obj);

    $this->uow->delete($obj);

    $this->assertTrue($this->uow->isDeleted($obj));

    $this->uow->evict($obj);

    $this->assertFalse($this->uow->isDeleted($obj));
  }

  function testDeletePurgeFromCache()
  {
    $obj = new UOWTestObject();
    $obj->set('id', $id = 1);

    $this->uow->registerExisting($obj);
    $this->uow->delete($obj);

    $this->uow->commit();

    $this->toolkit->setReturnReference('createObject', $null = null, array('UOWTestObject'));

    //making sure dao gets called since cache is empty
    $this->dao->expectOnce('fetchById', array($id));
    $this->dao->setReturnValue('fetchById', null, array($id));

    $this->assertNull($this->uow->load('UOWTestObject', $id));
  }
}

?>
