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
require_once(LIMB_DIR . '/core/cache/CacheRegistry.class.php');
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

class UOWTestObjectMapperStub extends MockAbstractDataMapper
{
  var $new_id = 0;

  function setId($id)
  {
    $this->new_id = $id;
  }

  function save(&$object)
  {
    parent :: save($object);

    if(!$object->get('id'))
      $object->set('id', $this->new_id);
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
    parent :: LimbTestCase('unit of work tests');
  }

  function setUp()
  {
    $this->uow = new UnitOfWork();
    $this->mapper = new UOWTestObjectMapperStub($this);
    $this->dao = new MockSQLBasedDAO($this);
    $this->cache = new CacheRegistry($this);

    $this->mapper->setReturnValue('getIdentityKeyName', 'id');

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnReference('getCache', $this->cache);

    $this->toolkit->setReturnReference('createDAO', $this->dao, array('UOWTestObjectDAO'));
    $this->toolkit->setReturnReference('createDataMapper', $this->mapper, array('UOWTestObjectMapper'));

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->mapper->tally();
    $this->dao->tally();
    $this->toolkit->tally();

    Limb :: restoreToolkit();
  }

  function testLoadNotFound()
  {
    $object = new UOWTestObject();

    $this->toolkit->setReturnReference('createObject', $object, array('UOWTestObject'));

    $this->dao->expectOnce('fetchById', array($id = 1));
    $this->dao->setReturnValue('fetchById', null, array($id));

    $this->mapper->expectNever('load');

    $this->assertNull($this->uow->load('UOWTestObject', $id));
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

    $this->assertEqual($object, $loaded_object);

    //cache hit test
    $object =& $this->uow->load('UOWTestObject', $id);

    $this->assertEqual($object, $loaded_object);
  }

  function testCommit()
  {
    $obj1 = new UOWTestObject();
    $obj1->set('id', $id1 = 1);

    $obj2 = new UOWTestObject();
    $obj2->set('id', $id2 = 2);

    $this->uow->register($obj1);
    $this->uow->register($obj2);

    $obj1->set('changed', true);

    $this->mapper->expectOnce('save', array($obj1));

    $this->uow->commit();
  }

  function testDirtyObjectsGetNewlyRegisteredAfterCommit()
  {
    $obj = new UOWTestObject();
    $obj->set('id', $id = 100);

    $this->uow->register($obj);

    $obj->set('changed', true);

    $this->mapper->expectOnce('save', array($obj));

    $this->uow->commit();

    $this->uow->commit();//saved only once, since we're not changing object
  }

  function testNewObject()
  {
    $obj = new UOWTestObject();

    $this->uow->register($obj);

    $this->mapper->setId($id = 1000);
    $this->mapper->expectOnce('save', array($obj));

    $this->uow->commit();

    $this->assertEqual($obj->get('id'), $id);

    $this->uow->commit();//saved only once, since we're not changing object
  }

  function testDeleteNew()
  {
    $obj = new UOWTestObject();

    $this->uow->delete($obj);

    $this->mapper->expectNever('delete');

    $this->uow->commit();
  }

  function testDelete()
  {
    $obj = new UOWTestObject();
    $obj->set('id', $id = 1);
    $this->uow->register($obj);

    $this->uow->delete($obj);

    $this->mapper->expectOnce('delete', array($obj));

    $this->uow->commit();

    $this->uow->commit();//deleted only once
  }

  function testDeletePurgeFromCache()
  {
    $obj = new UOWTestObject();
    $obj->set('id', $id = 1);

    $this->uow->register($obj);
    $this->uow->delete($obj);

    $this->mapper->expectOnce('delete', array($obj));

    $this->uow->commit();

    $this->toolkit->setReturnReference('createObject', $null = null, array('UOWTestObject'));

    $this->dao->expectOnce('fetchById', array($id));
    $this->dao->setReturnValue('fetchById', null, array($id));

    $this->assertNull($this->uow->load('UOWTestObject', $id));
  }

  function testEvictNewObject()
  {
    $obj = new UOWTestObject();

    $this->uow->register($obj);

    $this->uow->evict($obj);

    $this->mapper->expectNever('save', array(new IdenticalExpectation($obj)));

    $this->uow->commit();
  }

  function testEvictExistingObject()
  {
    $obj = new UOWTestObject();
    $obj->set('id', $id = 1);
    $this->uow->register($obj);

    $this->uow->evict($obj);

    $this->mapper->expectNever('save', array($obj));

    $this->uow->commit();
  }

  function testEvictToBeDeletedObject()
  {
    $obj = new UOWTestObject();
    $obj->set('id', $id = 1);

    $this->uow->register($obj);

    $this->uow->delete($obj);

    $this->uow->evict($obj);

    $this->mapper->expectNever('delete', array($obj));

    $this->uow->commit();
  }

  function testIsRegisteredForNewObject()
  {
    $obj1 = new UOWTestObject();
    $obj2 = new UOWTestObject();

    $this->uow->register($obj1);

    $this->assertTrue($this->uow->isRegistered($obj1));
    $this->assertFalse($this->uow->isRegistered($obj2));
  }

  function testIsDeleted()
  {
    $obj = new UOWTestObject();
    $obj->set('id', $id = 1);

    $this->uow->register($obj);

    $this->assertFalse($this->uow->isDeleted($obj));

    $this->uow->delete($obj);

    $this->assertTrue($this->uow->isDeleted($obj));
  }

  function testReset()
  {
    $obj1 = new UOWTestObject();
    $obj1->set('id', $id = 1);

    $obj2 = new UOWTestObject();

    $this->uow->register($obj1);
    $this->uow->register($obj2);

    $this->uow->reset();

    $this->assertFalse($this->uow->isRegistered($obj1));
    $this->assertFalse($this->uow->isRegistered($obj2));

    $this->assertFalse($this->uow->load('UOWTestObject', $id));
  }
}

?>
