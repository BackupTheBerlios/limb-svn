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
require_once(LIMB_DIR . '/core/data_mappers/SiteObjectBehaviourMapper.class.php');
require_once(LIMB_DIR . '/core/behaviours/SiteObjectBehaviour.class.php');

Mock :: generatePartial('SiteObjectBehaviourMapper',
                        'SiteObjectBehaviourMapperTestVersion',
                        array('insert', 'update'));

class SiteObjectBehaviourMapperTest extends LimbTestCase
{
  var $db;
  var $mapper;

  function SiteObjectBehaviourMapperTest()
  {
    parent :: LimbTestCase('site object behaviour mapper test');
  }

  function setUp()
  {
    $this->mapper = new SiteObjectBehaviourMapper();
    $this->db =& LimbDbPool :: getConnection();

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->sqlDelete('sys_behaviour');
  }

  function testFindByIdNull()
  {
    $this->assertNull($this->mapper->findById(1000));
  }

  function testFindById()
  {
    $this->db->sqlInsert('sys_behaviour', array('id' => $id = 100, 'name' => 'SiteObjectBehaviour'));

    $behaviour = $this->mapper->findById($id);

    $this->assertIsA($behaviour, 'SiteObjectBehaviour');
    $this->assertEqual($id, $behaviour->getId());
  }

  function testSaveInsert()
  {
    $mapper = new SiteObjectBehaviourMapperTestVersion($this);

    $behaviour = new SiteObjectBehaviour();

    $mapper->expectOnce('insert', array($behaviour));

    $mapper->save($behaviour);

    $mapper->tally();
  }

  function testSaveUpdate()
  {
    $mapper = new SiteObjectBehaviourMapperTestVersion($this);

    $behaviour = new SiteObjectBehaviour();
    $behaviour->setId(100);

    $mapper->expectOnce('update', array($behaviour));

    $mapper->save($behaviour);

    $mapper->tally();
  }

  function testInsert()
  {
    $behaviour = new SiteObjectBehaviour();

    $this->mapper->insert($behaviour);

    $this->db->sqlSelect('sys_behaviour', '*', 'id=' . $behaviour->getId());

    $record = $this->db->fetchRow();

    $this->assertEqual($record['name'], get_class($behaviour));
  }

  function testUpdateFailedNoId()
  {
    $behaviour = new SiteObjectBehaviour();

    $this->mapper->update($behaviour);
    $this->assertTrue(catch('Exception', $e));
  }

  function testUpdate()
  {
    $this->db->sqlInsert('sys_behaviour', array('id' => $id = 100));

    $behaviour = new SiteObjectBehaviour();
    $behaviour->setId($id);

    $this->mapper->update($behaviour);

    $this->db->sqlSelect('sys_behaviour', '*', 'id=' . $behaviour->getId());

    $record = $this->db->fetchRow();

    $this->assertEqual($record['name'], get_class($behaviour));
  }

  function testDeleteFailedNoId()
  {
    $behaviour = new SiteObjectBehaviour();

    $this->mapper->delete($behaviour);
    $this->assertTrue(catch('Exception', $e));
  }

  function testDelete()
  {
    $this->db->sqlInsert('sys_behaviour', array('id' => $id = 100));

    $behaviour = new SiteObjectBehaviour();
    $behaviour->setId($id);

    $this->mapper->delete($behaviour);

    $this->db->sqlSelect('sys_behaviour', '*', 'id=' . $behaviour->getId());

    $this->assertTrue(!$this->db->fetchRow());
  }

  function testGetIdsByNames()
  {
    $this->db->sqlInsert('sys_behaviour', array('id' => 10, 'name' => 'test1'));
    $this->db->sqlInsert('sys_behaviour', array('id' => 11, 'name' => 'test2'));
    $this->db->sqlInsert('sys_behaviour', array('id' => 12, 'name' => 'test3'));

    $ids = SiteObjectBehaviourMapper :: getIdsByNames(array('test1', 'test2'));

    sort($ids);
    $this->assertEqual(sizeof($ids), 2);
    $this->assertEqual($ids, array(10, 11));
  }
}

?>