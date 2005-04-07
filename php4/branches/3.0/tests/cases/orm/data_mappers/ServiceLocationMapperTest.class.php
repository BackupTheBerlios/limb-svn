<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: ServiceLocationMapperTest.class.php 1181 2005-03-21 10:46:55Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/data_mappers/ServiceLocationMapper.class.php');
require_once(LIMB_DIR . '/core/ServiceLocation.class.php');

class ServiceLocationMapperTest extends LimbTestCase
{
  var $db;

  function ServiceLocationMapperTest()
  {
    parent :: LimbTestCase('service location mapper test');
  }

  function setUp()
  {
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
    $this->db->delete('sys_object_to_service');
    $this->db->delete('sys_service');
  }

  function testLoad()
  {
    $mapper = new ServiceLocationMapper();
    $object = new ServiceLocation();

    $record = new Dataspace();
    $record->import(array('_service_name' => $service_name = 'TestService',
                          '_service_title' => $title = 'some title',
                          ));

    $mapper->load($record, $object);

    $this->assertEqual($object->get('name'), $service_name);
    $this->assertEqual($object->get('title'), $title);
  }

  function testFailedInsertNoOId()
  {
    $object = new ServiceLocation();

    $mapper = new ServiceLocationMapper();

    $mapper->insert($object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'oid is not set');
  }

  function testInsertOk()
  {
    $mapper = new ServiceLocationMapper();
    $object = new ServiceLocation();
    $object->set('name', $service_name = 'TestService');
    $object->set('title', $title = 'Test title');
    $object->set('oid', $oid = 100);

    $mapper->insert($object);

    $rs =& $this->db->select('sys_service');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]['name'], $service_name);
    $service_id = $arr[0]['id'];

    $rs =& $this->db->select('sys_object_to_service');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($arr[0]['oid'], $oid);
    $this->assertEqual($arr[0]['service_id'], $service_id);
    $this->assertEqual($arr[0]['title'], $title);
  }

  function testDontInsertServiceRecordTwice()
  {
    $this->db->insert('sys_service', array('id' => $service_id = 10,
                                           'name' => $service_name = 'TestService'));

    $mapper = new ServiceLocationMapper();
    $object = new ServiceLocation();
    $object->set('name', $service_name);
    $object->set('title', $title = 'Test title');
    $object->set('oid', $oid = 100);

    $mapper->insert($object);

    $rs =& $this->db->select('sys_service');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]['name'], $service_name);
    $this->assertEqual($arr[0]['id'], $service_id);
  }

  function testUpdateRecordExists()
  {
    $mapper = new ServiceLocationMapper();

    $this->db->insert('sys_service', array('id' => $service_id = 10,
                                           'name' => $service_name = 'TestService'));

    $this->db->insert('sys_object_to_service',
                      array('oid' => $oid = 100,
                            'service_id' => $old_service_id = 499,
                            'title' => 'Old title'));

    $object = new ServiceLocation();
    $object->set('name', $service_name);
    $object->set('title', $title = 'Test title');
    $object->set('oid', $oid);

    $mapper->update($object);

    $rs =& $this->db->select('sys_service');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]['name'], $service_name);


    $rs =& $this->db->select('sys_object_to_service');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($arr[0]['oid'], $oid);
    $this->assertEqual($arr[0]['service_id'], $service_id);
    $this->assertEqual($arr[0]['title'], $title);
  }

  function testUpdateRecordNotExists()
  {
    $mapper = new ServiceLocationMapper();

    $object = new ServiceLocation();
    $object->set('name', $service_name = 'TestService');
    $object->set('title', $title = 'Test title');
    $object->set('oid', $oid = 100);

    $mapper->update($object);

    $rs =& $this->db->select('sys_service');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]['name'], $service_name);
    $service_id = $arr[0]['id'];

    $rs =& $this->db->select('sys_object_to_service');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($arr[0]['oid'], $oid);
    $this->assertEqual($arr[0]['service_id'], $service_id);
    $this->assertEqual($arr[0]['title'], $title);
  }

  function testDelete()
  {
    $mapper = new ServiceLocationMapper();

    $this->db->insert('sys_object_to_service',
                      array('oid' => $oid = 100,
                            'service_id' => $service_id = 500,
                            'title' => 'some title'));

    // This record will stay
    $this->db->insert('sys_service',
                      array('id' => $service_id,
                            'name' => $service_name = 'TestService'));

    $object = new ServiceLocation();
    $object->set('oid', $oid);
    $object->set('name', $service_name);

    $mapper->delete($object);

    $rs =& $this->db->select('sys_service');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);

    $rs =& $this->db->select('sys_object_to_service');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 0);
  }
}

?>