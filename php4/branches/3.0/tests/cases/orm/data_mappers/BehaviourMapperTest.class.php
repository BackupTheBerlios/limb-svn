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
require_once(LIMB_DIR . '/core/data_mappers/BehaviourMapper.class.php');
require_once(LIMB_DIR . '/core/behaviours/Behaviour.class.php');
require_once(LIMB_DIR . '/core/db/SimpleDb.class.php');

Mock :: generatePartial('BehaviourMapper',
                        'BehaviourMapperTestVersion',
                        array('insert', 'update'));

class BehaviourMapperTest extends LimbTestCase
{
  var $db;
  var $mapper;

  function BehaviourMapperTest()
  {
    parent :: LimbTestCase('behaviour mapper test');
  }

  function setUp()
  {
    $this->mapper = new BehaviourMapper();
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
    $this->db->delete('sys_behaviour');
  }

  function testFindByIdNull()
  {
    $this->assertNull($this->mapper->findById(1000));
  }

  function testFindById()
  {
    $this->db->insert('sys_behaviour', array('id' => $id = 100, 'name' => 'Behaviour'));

    $behaviour = $this->mapper->findById($id);

    $this->assertIsA($behaviour, 'Behaviour');
    $this->assertEqual($id, $behaviour->getId());
  }

  function testSaveInsert()
  {
    $mapper = new BehaviourMapperTestVersion($this);

    $behaviour = new Behaviour('whatever');

    $mapper->expectOnce('insert', array($behaviour));

    $mapper->save($behaviour);

    $mapper->tally();
  }

  function testSaveUpdate()
  {
    $mapper = new BehaviourMapperTestVersion($this);

    $behaviour = new Behaviour('whatever');
    $behaviour->setId(100);

    $mapper->expectOnce('update', array($behaviour));

    $mapper->save($behaviour);

    $mapper->tally();
  }

  function testInsert()
  {
    $behaviour = new Behaviour($name = 'test');

    $this->mapper->insert($behaviour);

    $rs =& $this->db->select('sys_behaviour', '*', array('id' => $behaviour->getId()));

    $record = $rs->getRow();

    $this->assertEqual($record['name'], $name);
  }

  function testUpdateFailedNoId()
  {
    $behaviour = new Behaviour('whatever');

    $this->mapper->update($behaviour);
    $this->assertTrue(catch('Exception', $e));
  }

  function testUpdate()
  {
    $this->db->insert('sys_behaviour', array('id' => $id = 100));

    $behaviour = new Behaviour($name = 'test');
    $behaviour->setId($id);

    $this->mapper->update($behaviour);

    $rs =& $this->db->select('sys_behaviour', '*',  array('id' => $behaviour->getId()));

    $record = $rs->getRow();

    $this->assertEqual($record['name'], $name);
  }

  function testDeleteFailedNoId()
  {
    $behaviour = new Behaviour('whatever');

    $this->mapper->delete($behaviour);
    $this->assertTrue(catch('Exception', $e));
  }

  function testDelete()
  {
    $this->db->insert('sys_behaviour', array('id' => $id = 100));

    $behaviour = new Behaviour('whatever');
    $behaviour->setId($id);

    $this->mapper->delete($behaviour);

    $rs =& $this->db->select('sys_behaviour', '*',  array('id' => $behaviour->getId()));

    $this->assertTrue(!$rs->getRow());
  }

  function testGetIdsByNames()
  {
    $this->db->insert('sys_behaviour', array('id' => 10, 'name' => 'test1'));
    $this->db->insert('sys_behaviour', array('id' => 11, 'name' => 'test2'));
    $this->db->insert('sys_behaviour', array('id' => 12, 'name' => 'test3'));

    $ids = BehaviourMapper :: getIdsByNames(array('test1', 'test2'));

    sort($ids);
    $this->assertEqual(sizeof($ids), 2);
    $this->assertEqual($ids, array(10, 11));
  }
}

?>