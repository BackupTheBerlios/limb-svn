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
require_once(LIMB_DIR . '/core/Object.class.php');

Mock :: generate('AbstractDataMapper');
Mock :: generatePartial('LimbBaseToolkit', 'VersionedObjectMapperTestToolkit',
                 array('getUser'));

Mock :: generate('User');

class VersionedDataMapperStub extends MockAbstractDataMapper
{
  var $new_versioned_object_id;

  function getIdentityKeyName()
  {
    return 'versioned_object_id';
  }

  function setNewVersionedObjectId($id)
  {
    $this->new_versioned_object_id = $id;
  }

  function insert(&$object)
  {
    $object->set('versioned_object_id', $this->new_versioned_object_id);

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
    $this->user->setReturnValue('getId', 25);
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
    $object = new Object();

    $result = array('whatever');

    $record = new Dataspace();
    $record->import($result);

    $this->delegated_mapper->expectOnce('load', array($record, $object));

    $mapper->load($record, $object);
  }

  function testInsert()
  {
    $mapper = new VersionedObjectMapper($this->delegated_mapper);

    $object = new Object();
    $this->delegated_mapper->setNewVersionedObjectId($id = 100);

    $this->delegated_mapper->expectOnce('insert', array($object));

    $mapper->insert($object);

    $rs =& $this->db->select('sys_current_version', '*', array('revision_object_id' => $id));
    $record1 = $rs->getRow();

    $conditions['revision_object_id'] = $id;
    $conditions['version_session_id'] = $record1['version_session_id'];

    $rs =& $this->db->select('sys_version_history', '*', $conditions);
    $record2 = $rs->getRow();

    $this->assertEqual($record2['revision_object_id'], $id);
    $this->assertEqual($record2['version'], 1);
    $this->assertNotNull($record2['creator_id']);
    $this->assertTrue($record2['created_date'] > time() - 100);
  }

  function testUpdate()
  {
    $object = new Object();

    $mapper = new VersionedObjectMapper($this->delegated_mapper);

    $object->set('versioned_object_id', $revision_object_id = 99);

    $this->delegated_mapper->setNewVersionedObjectId($new_versioned_object_id = 100);

    $this->delegated_mapper->expectOnce('insert', array($object));
    $this->delegated_mapper->expectNever('update');

    $this->db->insert('sys_version_history',
                     array('revision_object_id' => $revision_object_id,
                           'version_session_id' => $version_session_id = 50,
                           'version' => 2));

    // This record should be updated
    $this->db->insert('sys_current_version',
                     array('revision_object_id' => $revision_object_id,
                           'version_session_id' => $version_session_id));

    $mapper->update($object);

    $rs =& $this->db->select('sys_version_history');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 2);

    $record = reset($arr);
    $this->assertEqual($record['revision_object_id'], $revision_object_id);
    $this->assertEqual($record['version_session_id'], $version_session_id);
    $this->assertEqual($record['version'], 2);

    $record = next($arr);
    $this->assertEqual($record['revision_object_id'], $new_versioned_object_id);
    $this->assertEqual($record['version_session_id'], $version_session_id);
    $this->assertEqual($record['version'], 3);

    $rs =& $this->db->select('sys_current_version');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($arr[0]['revision_object_id'], $new_versioned_object_id);
    $this->assertEqual($arr[0]['version_session_id'], $version_session_id);
  }

  function testDelete()
  {
    $object = new Object();
    $mapper = new VersionedObjectMapper($this->delegated_mapper);

    $object->set('versioned_object_id', $revision_object_id = 101);

    $this->db->insert('sys_version_history',
                         array('revision_object_id' => $revision_object_id,
                               'version_session_id' => $version_session_id = 1000,
                               'version' => 1));

    $this->db->insert('sys_version_history',
                         array('revision_object_id' => 101,
                               'version_session_id' => $version_session_id,
                               'version' => 2));

    // This record will stay
    $this->db->insert('sys_version_history',
                         array('revision_object_id' => 102,
                               'version_session_id' => $will_stay_version_session_id = 1001,
                               'version' => 2));

    $this->db->insert('sys_current_version',
                         array('revision_object_id' => 101,
                               'version_session_id' => $version_session_id));

    // This record will stay
    $this->db->insert('sys_current_version',
                     array('revision_object_id' => 102,
                           'version_session_id' => $will_stay_version_session_id));

    $this->delegated_mapper->expectOnce('delete', array($object));

    $mapper->delete($object);

    $rs =& $this->db->select('sys_version_history');
    $array = $rs->getArray();

    $this->assertEqual(sizeof($array), 1);
    $this->assertEqual($array[0]['version_session_id'], $will_stay_version_session_id);

    $rs =& $this->db->select('sys_current_version');
    $array = $rs->getArray();

    $this->assertEqual(sizeof($array), 1);
    $this->assertEqual($array[0]['version_session_id'], $will_stay_version_session_id);
  }
}

?>