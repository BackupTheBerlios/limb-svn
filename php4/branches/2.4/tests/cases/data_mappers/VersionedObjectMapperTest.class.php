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
require_once(LIMB_DIR . '/core/site_objects/VersionedSiteObject.class.php');

Mock :: generate('AbstractDataMapper');
Mock :: generatePartial('LimbBaseToolkit', 'VersionedObjectMapperTestToolkit',
                 array('getUser'));

Mock :: generate('User');

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

    $this->delegated_mapper = new MockAbstractDataMapper($this);
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
    $domain_object = new VersionedSiteObject();

    $result = array('id' => $id = 10,
                    'version_uid' => $version_uid = 100,
                    'version' => $version = 2);

    $record = new Dataspace();
    $record->import($result);

    $this->delegated_mapper->expectOnce('load', array($record, $domain_object));

    $mapper->load($record, $domain_object);

    $this->assertEqual($domain_object->getVersionUid(),$version_uid);
    $this->assertEqual($domain_object->getVersion(),$version);
  }

  function testInsert()
  {
    $mapper = new VersionedObjectMapper($this->delegated_mapper);

    $domain_object = new VersionedSiteObject();
    $domain_object->setId($id = 100);

    $this->delegated_mapper->expectOnce('insert', array($domain_object));

    $mapper->insert($domain_object);

    $this->assertTrue($domain_object->getVersionUid(), 1); // first uid generated value

    $this->_checkSysVersionHistoryRecord($domain_object);
  }

  function testVersionedUpdate()
  {
    $domain_object = new VersionedSiteObject();

    $mapper = new VersionedObjectMapper($this->delegated_mapper);

    $domain_object->setId($uid = 100);
    $domain_object->setVersionUid($version_uid = 30);
    $domain_object->setVersion(1);
    $domain_object->increaseVersion();

    $this->delegated_mapper->expectOnce('insert', array($domain_object));
    $this->delegated_mapper->expectNever('update');

    // This record should be updated
    $this->db->insert('sys_current_version',
                     array('uid' => 99,
                           'version_uid' => $version_uid));

    $mapper->update($domain_object);

    $this->assertEqual($domain_object->getVersion(), 2);

    $rs =& $this->db->select('sys_version_history');
    $this->assertEqual(sizeof($rs->getArray()), 1);

    $rs =& $this->db->select('sys_current_version');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($arr[0]['uid'], $uid);
    $this->assertEqual($arr[0]['version_uid'], $version_uid);

    $this->_checkSysVersionHistoryRecord($domain_object);
  }

  function testNonversionedUpdateOk()
  {
    $mapper = new VersionedObjectMapper($this->delegated_mapper);

    $domain_object = new VersionedSiteObject();

    $domain_object->setId($object_id = 100);
    $domain_object->setVersionUid($version_uid = 100);
    $domain_object->setVersion($version = 1);

    $this->delegated_mapper->expectOnce('update', array($domain_object));
    $this->delegated_mapper->expectNever('insert');

    $mapper->update($domain_object);

    $rs =& $this->db->select('sys_version_history');
    $this->assertEqual(sizeof($rs->getArray()), 0);

    $rs =& $this->db->select('sys_current_version');
    $this->assertEqual(sizeof($rs->getArray()), 0);
  }

  function testDelete()
  {
    $domain_object = new VersionedSiteObject();
    $mapper = new VersionedObjectMapper($this->delegated_mapper);

    $domain_object->setId($uid = 100);
    $domain_object->setVersionUid($version_uid = 1000);

    $this->db->insert('sys_version_history',
                         array('uid' => $uid,
                               'version_uid' => $version_uid,
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

  function _checkSysVersionHistoryRecord($domain_object)
  {
    $conditions['uid'] = $domain_object->getId();
    $conditions['version_uid'] = $domain_object->getVersionUid();

    $rs =& $this->db->select('sys_version_history', '*', $conditions);
    $record = $rs->getRow();

    $this->assertEqual($record['uid'], $domain_object->getId());
    $this->assertEqual($record['version_uid'], $domain_object->getVersionUid());
    $this->assertEqual($record['version'], $domain_object->getVersion());
    $this->assertNotNull($record['creator_id']);
    $this->assertTrue($record['created_date'] > time() - 100);
  }
}

?>