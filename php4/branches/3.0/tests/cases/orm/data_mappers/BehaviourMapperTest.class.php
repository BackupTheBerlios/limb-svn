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
require_once(LIMB_DIR . '/core/data_mappers/ServiceMapper.class.php');
require_once(LIMB_DIR . '/core/services/Service.class.php');
require_once(LIMB_DIR . '/core/db/SimpleDb.class.php');

Mock :: generatePartial('ServiceMapper',
                        'ServiceMapperTestVersion',
                        array('insert', 'update'));

class ServiceMapperTest extends LimbTestCase
{
  var $db;
  var $mapper;

  function ServiceMapperTest()
  {
    parent :: LimbTestCase('service mapper test');
  }

  function setUp()
  {
    $this->mapper = new ServiceMapper();
    $toolkit =& Limb :: toolkit();
    $this->db =& new SimpleDb($toolkit->getDbConnection());

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_service');
  }

  function testFindByIdNull()
  {
    $this->assertNull($this->mapper->findById(1000));
  }

  function testFindById()
  {
    $this->db->insert('sys_service', array('id' => $id = 100, 'name' => 'Service'));

    $service = $this->mapper->findById($id);

    $this->assertIsA($service, 'Service');
    $this->assertEqual($id, $service->getId());
  }

  function testSaveInsert()
  {
    $mapper = new ServiceMapperTestVersion($this);

    $service = new Service('whatever');

    $mapper->expectOnce('insert', array($service));

    $mapper->save($service);

    $mapper->tally();
  }

  function testSaveUpdate()
  {
    $mapper = new ServiceMapperTestVersion($this);

    $service = new Service('whatever');
    $service->setId(100);

    $mapper->expectOnce('update', array($service));

    $mapper->save($service);

    $mapper->tally();
  }

  function testInsert()
  {
    $service = new Service($name = 'test');

    $this->mapper->insert($service);

    $rs =& $this->db->select('sys_service', '*', array('id' => $service->getId()));

    $record = $rs->getRow();

    $this->assertEqual($record['name'], $name);
  }

  function testDoNotInsertTheSameRecortTwice()
  {
    $this->db->insert('sys_service', array('id' => $id = 10,
                                             'name' => $name = 'test'));

    $service = new Service($name);

    $this->mapper->insert($service);

    $rs =& $this->db->select('sys_service', '*');

    $this->assertEqual($rs->getTotalRowCount(), 1);
    $this->assertEqual($service->getId(), $id);
  }

  function testUpdateFailedNoId()
  {
    $service = new Service('whatever');

    $this->mapper->update($service);
    $this->assertTrue(catch('Exception', $e));
  }

  function testUpdate()
  {
    $this->db->insert('sys_service', array('id' => $id = 100));

    $service = new Service($name = 'test');
    $service->setId($id);

    $this->mapper->update($service);

    $rs =& $this->db->select('sys_service', '*',  array('id' => $service->getId()));

    $record = $rs->getRow();

    $this->assertEqual($record['name'], $name);
  }

  function testDeleteFailedNoId()
  {
    $service = new Service('whatever');

    $this->mapper->delete($service);
    $this->assertTrue(catch('Exception', $e));
  }

  function testDelete()
  {
    $this->db->insert('sys_service', array('id' => $id = 100));

    $service = new Service('whatever');
    $service->setId($id);

    $this->mapper->delete($service);

    $rs =& $this->db->select('sys_service', '*',  array('id' => $service->getId()));

    $this->assertTrue(!$rs->getRow());
  }

  function testGetIdsByNames()
  {
    $this->db->insert('sys_service', array('id' => 10, 'name' => 'test1'));
    $this->db->insert('sys_service', array('id' => 11, 'name' => 'test2'));
    $this->db->insert('sys_service', array('id' => 12, 'name' => 'test3'));

    $ids = ServiceMapper :: getIdsByNames(array('test1', 'test2'));

    sort($ids);
    $this->assertEqual(sizeof($ids), 2);
    $this->assertEqual($ids, array(10, 11));
  }
}

?>