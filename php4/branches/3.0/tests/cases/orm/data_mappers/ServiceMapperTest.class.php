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
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');
require_once(LIMB_DIR . '/core/data_mappers/ServiceMapper.class.php');
require_once(LIMB_DIR . '/core/data_mappers/BehaviourMapper.class.php');
require_once(LIMB_DIR . '/core/Service.class.php');
require_once(LIMB_DIR . '/core/behaviours/Behaviour.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                      'ServiceMapperTestToolkit',
                      array('createDataMapper'));

Mock :: generate('Service');
Mock :: generate('Behaviour');
Mock :: generate('BehaviourMapper');

class ServiceMapperTest extends LimbTestCase
{
  var $db;
  var $behaviour;
  var $behaviour_mapper;
  var $service;
  var $toolkit;

  function ServiceMapperTest()
  {
    parent :: LimbTestCase('service mapper test');
  }

  function setUp()
  {
    $this->toolkit = new ServiceMapperTestToolkit($this);
    $this->behaviour_mapper = new MockBehaviourMapper($this);
    $this->service = new MockService($this);

    $this->toolkit->setReturnReference('createDataMapper',
                                   $this->behaviour_mapper,
                                   array('BehaviourMapper'));

    $this->behaviour = new MockBehaviour($this);

    Limb :: registerToolkit($this->toolkit);

    $this->db =& new SimpleDb(LimbDbPool :: getConnection());

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();

    $this->toolkit->tally();
    $this->service->tally();
    $this->behaviour->tally();
    $this->behaviour_mapper->tally();

    Limb :: restoreToolkit();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_service');
    $this->db->delete('sys_behaviour');
  }

  function testLoad()
  {
    $record = new Dataspace();
    $record->import(array('service_id' => $service_id = 10,
                          'behaviour_id' => $behaviour_id = 100,
                          'title' => $title = 'title'));

    $mapper = new ServiceMapper();

    $this->behaviour_mapper->expectOnce('findById', array($behaviour_id));
    $this->behaviour_mapper->setReturnReference('findById', $this->behaviour, array($behaviour_id));

    $service = new Service();

    $mapper->load($record, $service);

    $this->assertEqual($service->getServiceId(), $service_id);
    $this->assertEqual($service->getTitle(), $title);

    $this->assertIsA($service->getBehaviour(), get_class($this->behaviour));
  }

  function testFailedInsertServiceRecordNoBehaviourAttached()
  {
    $service = new Service();

    $mapper = new ServiceMapper();

    $mapper->insert($service);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'behaviour is not attached');
  }

  function testInsertServiceRecordOk()
  {
    $mapper = new ServiceMapper();

    $service = new Service();
    $service->set('oid', $oid = 100);//note 'id' becomes 'oid' in db!!!
    $service->setTitle($title = 'test');
    $service->attachBehaviour($this->behaviour);
    $this->behaviour->setReturnValue('getId', $behaviour_id = 25);

    $this->behaviour_mapper->expectOnce('save', array(new IsAExpectation('MockBehaviour')));

    $mapper->insert($service);

    $rs =& $this->db->select('sys_service');
    $services = $rs->getArray();
    $this->assertEqual(sizeof($services), 1);

    $this->assertNotNull($services[0]['service_id']);
    $this->assertEqual($service->getServiceId(), $services[0]['service_id']);
    $this->assertEqual($oid, $services[0]['oid']);
    $this->assertEqual($title, $services[0]['title']);
    $this->assertEqual($behaviour_id, $services[0]['behaviour_id']);
  }

  function testUpdateNoId()
  {
    $mapper = new ServiceMapper();
    $service = new Service();

    $mapper->update($service);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'service id not set');
  }

  function  testUpdateFailedNoBehaviourId()
  {
    $mapper = new ServiceMapper();
    $service = new Service();
    $service->setServiceId(1);

    $mapper->update($service);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'behaviour not attached');
  }

  function testUpdateServiceRecordOk()
  {
    $old_row = array('service_id' => $service_id = 1,
                     'behaviour_id' => $behaviour_id = 10,
                     'oid' => $oid = 100,
                     'title' => $title = 'title');

    $this->db->insert('sys_service', $old_row);

    $mapper = new ServiceMapper();

    $service = new Service();
    $service->set('oid', $new_oid = 101);
    $service->setServiceId($service_id);
    $service->setTitle($new_title = 'test2');
    $service->attachBehaviour($this->behaviour);
    $this->behaviour->setReturnValue('getId', $new_behaviour_id = 25);

    $this->behaviour_mapper->expectOnce('save', array(new IsAExpectation('MockBehaviour')));

    $mapper->update($service);

    $rs =& $this->db->select('sys_service');
    $services = $rs->getArray();
    $this->assertEqual(sizeof($services), 1);

    $this->assertEqual($service_id, $services[0]['service_id']);
    $this->assertEqual($new_title, $services[0]['title']);
    $this->assertEqual($new_oid, $services[0]['oid']);
    $this->assertEqual($new_behaviour_id, $services[0]['behaviour_id']);

  }

  function testCantDeleteNoId()
  {
    $service = new Service();
    $mapper = new ServiceMapper();

    $mapper->delete($service);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'service id not set');
  }

  function testDelete()
  {
    $service = new Service();
    $mapper = new ServiceMapper();

    $this->db->insert('sys_service', array('service_id' => $id = 1));
    $this->db->insert('sys_service', array('service_id' => 2));

    $service->setServiceId($id);

    $mapper->delete($service);

    $rs = $this->db->select('sys_service');
    $this->assertEqual(sizeof($rs->getArray()), 1);
  }
}

?>