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
require_once(LIMB_DIR . '/core/data_mappers/Object2NodeMapper.class.php');
require_once(LIMB_DIR . '/core/Object.class.php');

class Object2NodeMapperTest extends LimbTestCase
{
  var $db;

  function Object2NodeMapperTest()
  {
    parent :: LimbTestCase('object 2 node mapper test');
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
    $this->db->delete('sys_object_to_node');
  }

  function testFailedInsertNoOId()
  {
    $object = new Object();

    $mapper = new Object2NodeMapper();

    $mapper->insert($object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'oid is not set');
  }

  function testFailedInsertNoObjectId()
  {
    $object = new Object();
    $object->set('oid', 10);

    $mapper = new Object2NodeMapper();

    $mapper->insert($object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'node id is not set');
  }

  function testInsertOk()
  {
    $mapper = new Object2NodeMapper();
    $object = new Object();

    $object->set('oid', $oid = 100);
    $object->set('node_id', $node_id = 500);

    $mapper->insert($object);

    $rs =& $this->db->select('sys_object_to_node');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($arr[0]['oid'], $oid);
    $this->assertEqual($arr[0]['node_id'], $node_id);
  }

  function testUpdate()
  {
    $mapper = new Object2NodeMapper();
    $object = new Object();

    $object->set('oid', $oid = 100);
    $object->set('node_id', $node_id = 500);

    $this->db->insert('sys_object_to_node',
                      array('oid' => $oid,
                            'node_id' => $old_node_id = 499));

    $mapper->update($object);

    $rs =& $this->db->select('sys_object_to_node');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($arr[0]['oid'], $oid);
    $this->assertEqual($arr[0]['node_id'], $node_id);
  }

  function testDelete()
  {
    $mapper = new Object2NodeMapper();
    $object = new Object();

    $object->set('oid', $oid = 100);

    $this->db->insert('sys_object_to_node',
                      array('oid' => $oid,
                            'node_id' => $node_id = 500));

    // This record will stay
    $this->db->insert('sys_object_to_node',
                      array('oid' => $oid_to_stay = 102,
                            'node_id' => $node_id_to_stay = 502));

    $mapper->delete($object);

    $rs =& $this->db->select('sys_object_to_node');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($arr[0]['oid'], $oid_to_stay);
    $this->assertEqual($arr[0]['node_id'], $node_id_to_stay);
  }
}

?>