<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: Object2ServiceMapperTest.class.php 1181 2005-03-21 10:46:55Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/data_mappers/Object2ServiceMapper.class.php');
require_once(LIMB_DIR . '/core/Object.class.php');

class Object2ServiceMapperTest extends LimbTestCase
{
  var $db;

  function Object2ServiceMapperTest()
  {
    parent :: LimbTestCase('object 2 service mapper test');
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
    $mapper = new Object2ServiceMapper();
    $object = new Object();

    $record = new Dataspace();
    $record->import(array('service_name' => $service_name = 'TestService',
                          'title' => $title = 'some title',
                          ));

    $mapper->load($record, $object);

    $this->assertEqual($object->get('service_name'), $service_name);
    $this->assertEqual($object->get('title'), $title);
  }

  function testFailedInsertNoOId()
  {
    $object = new Object();

    $mapper = new Object2ServiceMapper();

    $mapper->insert($object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'oid is not set');
  }

  function testInsertOk()
  {
    $mapper = new Object2ServiceMapper();
    $object = new Object();
    $object->set('service_name', $service_name = 'TestService');
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

    $mapper = new Object2ServiceMapper();
    $object = new Object();
    $object->set('service_name', $service_name);
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
    $mapper = new Object2ServiceMapper();

    $this->db->insert('sys_service', array('id' => $service_id = 10,
                                           'name' => $service_name = 'TestService'));

    $this->db->insert('sys_object_to_service',
                      array('oid' => $oid = 100,
                            'service_id' => $old_service_id = 499,
                            'title' => 'Old title'));

    $object = new Object();
    $object->set('service_name', $service_name);
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
    $mapper = new Object2ServiceMapper();

    $object = new Object();
    $object->set('service_name', $service_name = 'TestService');
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
    $mapper = new Object2ServiceMapper();

    $this->db->insert('sys_object_to_service',
                      array('oid' => $oid = 100,
                            'service_id' => $service_id = 500,
                            'title' => 'some title'));

    // This record will stay
    $this->db->insert('sys_service',
                      array('id' => $service_id,
                            'name' => $service_name = 'TestService'));

    $object = new Object();
    $object->set('oid', $oid);
    $object->set('service_name', $service_name);

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