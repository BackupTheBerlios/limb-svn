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
require_once(LIMB_DIR . '/core/data_mappers/ObjectMapper.class.php');
require_once(LIMB_DIR . '/core/Object.class.php');

class ObjectMapperTest extends LimbTestCase
{
  var $db;

  function ObjectMapperTest()
  {
    parent :: LimbTestCase('object mapper test');
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
    $this->db->delete('sys_object');
    $this->db->delete('sys_class');
    $this->db->delete('sys_uid');
  }

  function testLoad()
  {
    $mapper = new ObjectMapper();
    $object = new Object();

    $record = new Dataspace();
    $record->import(array('oid' => $id = 10,
                          'class_id' => $class_id = 100));

    $mapper->load($record, $object);

    $this->assertEqual($object->get('oid'), $id);
    $this->assertEqual($object->get('class_id'), $class_id);
  }

  function testInsertOkGenerateClassId()
  {
    $mapper = new ObjectMapper();
    $object = new Object();
    $object->__class_name = 'Object';

    $mapper->insert($object);

    $rs =& $this->db->select('sys_class');
    $classes = $rs->getArray();
    $this->assertEqual(sizeof($classes), 1);
    $this->assertEqual($classes[0]['name'], $object->__class_name);

    $rs =& $this->db->select('sys_object');
    $objects = $rs->getArray();
    $this->assertEqual(sizeof($objects), 1);

    $this->assertEqual($objects[0]['oid'], 1);
    $this->assertEqual($objects[0]['class_id'], $classes[0]['id']);

    $this->assertEqual($object->get('oid'), $objects[0]['oid']);
    $this->assertEqual($object->get('class_id'), $classes[0]['id']);
  }

  function testInsertUseExistingClassId()
  {
    $mapper = new ObjectMapper();
    $object = new Object();
    $object->__class_name = 'Object';

    $this->db->insert('sys_class', array('id' => $class_id = 5,
                                         'name' => $object->__class_name));

    $mapper->insert($object);

    $rs =& $this->db->select('sys_class');
    $classes = $rs->getArray();
    $this->assertEqual(sizeof($classes), 1);

    $rs =& $this->db->select('sys_object');
    $objects = $rs->getArray();
    $this->assertEqual(sizeof($objects), 1);

    $this->assertEqual($objects[0]['oid'], 1);
    $this->assertEqual($objects[0]['class_id'], $class_id);

    $this->assertEqual($object->get('oid'), $objects[0]['oid']);
    $this->assertEqual($object->get('class_id'), $class_id);
  }

  function testDelete()
  {
    $mapper = new ObjectMapper();
    $object = new Object();
    $object->__class_name = 'Object';

    $this->db->insert('sys_class', array('id' => $class_id = 5,
                                         'name' => $object->__class_name));

    $this->db->insert('sys_object', array('oid' => $id1 = 1,
                                         'class_id' => $class_id));

    $this->db->insert('sys_object', array('oid' => $id2 = 2,
                                         'class_id' => $class_id));


    $object->set('oid', $id1);
    $object->set('class_id', $class_id);

    $mapper->delete($object);

    $rs =& $this->db->select('sys_class');
    $classes = $rs->getArray();
    $this->assertEqual(sizeof($classes), 1);

    $rs =& $this->db->select('sys_object');
    $objects = $rs->getArray();
    $this->assertEqual(sizeof($objects), 1);

    $this->assertEqual($objects[0]['oid'], $id2);
    $this->assertEqual($objects[0]['class_id'], $class_id);
  }
}

?>