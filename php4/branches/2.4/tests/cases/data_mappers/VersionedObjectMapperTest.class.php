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
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/permissions/User.class.php');
require_once(LIMB_DIR . '/core/data_mappers/AbstractDataMapper.class.php');
require_once(LIMB_DIR . '/core/data_mappers/VersionedObjectMapper.class.php');
require_once(LIMB_DIR . '/core/DomainObject.class.php');

Mock :: generate('AbstractDataMapper');
Mock :: generatePartial('LimbBaseToolkit', 'VersionedObjectMapperTestToolkit',
                 array('getUser'));

Mock :: generate('User');

class VersionedDataMapperStub extends MockAbstractDataMapper
{
  var $new_uid;

  function setNewUID($id)
  {
    $this->new_uid = $id;
  }

  function insert(&$object)
  {
    $object->setId($this->new_uid);

    parent :: insert($object);
  }
}

class VersionedObjectMapperTest extends LimbTestCase
{
  var $db;
  var $delegated_mapper;
  var $toolkit;

  function VersionedObjectMapperTest()
  {
    parent :: LimbTestCase('versioned object mapper test');
  }

  function setUp()
  {
    $this->toolkit = new VersionedObjectMapperTestToolkit($this);
    $this->user = new MockUser($this);
    $this->user->setReturnValue('getID', 25);
    $this->toolkit->setReturnReference('getUser', $this->user);

    $this->delegated_mapper = new VersionedDataMapperStub($this);
    $this->db =& new SimpleDb(LimbDbPool :: getConnection());

    $this->_cleanUp();

    Limb :: registerToolkit($this->toolkit);

  }

  function tearDown()
  {
    $this->delegated_mapper->tally();
    $this->toolkit->tally();

    $this->_cleanUp();

    Limb :: popToolkit();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_version_history');
    $this->db->delete('sys_current_version');
    $this->db->delete('sys_uid');
  }

  function testLoad()
  {
    $mapper = new VersionedObjectMapper($this->delegated_mapper);
    $domain_object = new DomainObject();

    $result = array('id' => 10);

    $record = new Dataspace();
    $record->import($result);

    $this->delegated_mapper->expectOnce('load', array($record, $domain_object));

    $mapper->load($record, $domain_object);
  }

  function testInsert()
  {
    $mapper = new VersionedObjectMapper($this->delegated_mapper);

    $domain_object = new DomainObject();
    $domain_object->setId($id = 100);

    $this->delegated_mapper->expectOnce('insert', array($domain_object));

    $mapper->insert($domain_object);

    $rs =& $this->db->select('sys_current_version', '*', array('uid' => $domain_object->getId()));
    $record1 = $rs->getRow();

    $conditions['uid'] = $domain_object->getId();
    $conditions['version_uid'] = $record1['version_uid'];

    $rs =& $this->db->select('sys_version_history', '*', $conditions);
    $record2 = $rs->getRow();

    $this->assertEqual($record2['uid'], $domain_object->getId());
    $this->assertEqual($record2['version'], 1);
    $this->assertNotNull($record2['creator_id']);
    $this->assertTrue($record2['created_date'] > time() - 100);
  }

  function testUpdate()
  {
    $domain_object = new DomainObject();

    $mapper = new VersionedObjectMapper($this->delegated_mapper);

    $domain_object->setId($uid = 99);

    $this->delegated_mapper->setNewUID($new_uid = 100);

    $this->delegated_mapper->expectOnce('insert', array($domain_object));
    $this->delegated_mapper->expectNever('update');

    $this->db->insert('sys_version_history',
                     array('uid' => $uid,
                           'version_uid' => $version_uid = 50,
                           'version' => 2));

    // This record should be updated
    $this->db->insert('sys_current_version',
                     array('uid' => $uid,
                           'version_uid' => $version_uid));

    $mapper->update($domain_object);

    $rs =& $this->db->select('sys_version_history');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 2);

    $record = reset($arr);
    $this->assertEqual($record['uid'], $uid);
    $this->assertEqual($record['version_uid'], $version_uid);
    $this->assertEqual($record['version'], 2);

    $record = next($arr);
    $this->assertEqual($record['uid'], $new_uid);
    $this->assertEqual($record['version_uid'], $version_uid);
    $this->assertEqual($record['version'], 3);

    $rs =& $this->db->select('sys_current_version');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($arr[0]['uid'], $new_uid);
    $this->assertEqual($arr[0]['version_uid'], $version_uid);
  }

  function testDelete()
  {
    $domain_object = new DomainObject();
    $mapper = new VersionedObjectMapper($this->delegated_mapper);

    $domain_object->setId($uid = 101);

    $this->db->insert('sys_version_history',
                         array('uid' => $uid,
                               'version_uid' => $version_uid = 1000,
                               'version' => 1));

    $this->db->insert('sys_version_history',
                         array('uid' => 101,
                               'version_uid' => $version_uid,
                               'version' => 2));

    // This record will stay
    $this->db->insert('sys_version_history',
                         array('uid' => 102,
                               'version_uid' => $will_stay_version_uid = 1001,
                               'version' => 2));

    $this->db->insert('sys_current_version',
                         array('uid' => 101,
                               'version_uid' => $version_uid));

    // This record will stay
    $this->db->insert('sys_current_version',
                     array('uid' => 102,
                           'version_uid' => $will_stay_version_uid));

    $this->delegated_mapper->expectOnce('delete', array($domain_object));

    $mapper->delete($domain_object);

    $rs =& $this->db->select('sys_version_history');
    $array = $rs->getArray();

    $this->assertEqual(sizeof($array), 1);
    $this->assertEqual($array[0]['version_uid'], $will_stay_version_uid);

    $rs =& $this->db->select('sys_current_version');
    $array = $rs->getArray();

    $this->assertEqual(sizeof($array), 1);
    $this->assertEqual($array[0]['version_uid'], $will_stay_version_uid);
  }
}

?>