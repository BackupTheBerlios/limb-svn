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
require_once(LIMB_DIR . '/core/data_mappers/SiteObjectMapper.class.php');
require_once(LIMB_DIR . '/core/data_mappers/SiteObjectBehaviourMapper.class.php');
require_once(LIMB_DIR . '/core/site_objects/SiteObject.class.php');
require_once(LIMB_DIR . '/core/behaviours/SiteObjectBehaviour.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/permissions/User.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                      'SiteObjectManipulationTestToolkit',
                      array('getUser', 'constant', 'createDataMapper'));

Mock :: generate('User');
Mock :: generate('SiteObject');
Mock :: generate('SiteObjectBehaviour');
Mock :: generate('SiteObjectBehaviourMapper');

Mock :: generatePartial('SiteObjectMapper',
                      'SiteObjectMapperTestVersion',
                      array('_getBehaviourMapper', 'getClassId'));

class SiteObjectMapperTest extends LimbTestCase
{
  var $db;
  var $behaviour;
  var $behaviour_mapper;
  var $site_object;
  var $toolkit;
  var $user;

  function SiteObjectMapperTest()
  {
    parent :: LimbTestCase('site object mapper test');
  }

  function setUp()
  {
    $this->toolkit = new SiteObjectManipulationTestToolkit($this);
    $this->user = new MockUser($this);
    $this->behaviour_mapper = new MockSiteObjectBehaviourMapper($this);
    $this->site_object = new MockSiteObject($this);
    $this->user->setReturnValue('getId', 125);

    $this->toolkit->setReturnReference('getUser', $this->user);
    $this->toolkit->setReturnReference('createDataMapper',
                                   $this->behaviour_mapper,
                                   array('SiteObjectBehaviourMapper'));

    $this->behaviour = new MockSiteObjectBehaviour($this);

    Limb :: registerToolkit($this->toolkit);

    $this->db =& new SimpleDb(LimbDbPool :: getConnection());

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();

    $this->toolkit->tally();
    $this->site_object->tally();
    $this->behaviour->tally();
    $this->behaviour_mapper->tally();

    Limb :: popToolkit();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_site_object');
    $this->db->delete('sys_class');
  }

  function testGetClassId()
  {
    $mapper = new SiteObjectMapper();
    $object = new SiteObject();

    // autogenerate class_id
    $id = $mapper->getClassId($object);

    $rs = $this->db->select('sys_class', '*', array('name' => get_class($object)));
    $arr = $rs->getRow();

    $this->assertNotNull($id);

    $this->assertEqual($id, $arr['id']);

    // generate class_id only once
    $id = $mapper->getClassId($object);
    $rs =& $this->db->select('sys_class', '*');
    $arr = $rs->getArray();

    $this->assertEqual(sizeof($arr), 1);
  }

  function testLoad()
  {
    $record = new Dataspace();
    $record->import(array('id' => $id = 10,
                          'behaviour_id' => $behaviour_id = 100,
                          'locale_id' => $locale_id = 'en',
                          'class_id' => $class_id = 200,
                          'title' => $title = 'title',
                          'modified_date' => $modified_date = time(),
                          'created_date' => $created_date = time() + 100,));

    $mapper = new SiteObjectMapperTestVersion($this);

    $mapper->setReturnReference('_getBehaviourMapper', $this->behaviour_mapper);

    $this->behaviour_mapper->expectOnce('findById', array($behaviour_id));
    $this->behaviour_mapper->setReturnReference('findById', $this->behaviour, array($behaviour_id));

    $site_object = new SiteObject();

    $mapper->load($record, $site_object);

    $this->assertEqual($site_object->getSiteObjectId(), $id);
    $this->assertEqual($site_object->getLocaleId(), $locale_id);
    $this->assertEqual($site_object->getClassId(), $class_id);
    $this->assertEqual($site_object->getTitle(), $title);
    $this->assertEqual($site_object->getModifiedDate(), $modified_date);
    $this->assertEqual($site_object->getCreatedDate(), $created_date);

    $this->assertIsA($site_object->getBehaviour(), get_class($this->behaviour));

    $mapper->tally();
  }

  function testFailedInsertSiteObjectRecordNoBehaviourAttached()
  {
    $site_object = new SiteObject();

    $mapper = new SiteObjectMapper();

    $mapper->insert($site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'behaviour is not attached');
  }

  function testInsertSiteObjectRecordOk()
  {
    $mapper = new SiteObjectMapperTestVersion($this);
    $mapper->setReturnValue('getClassId', 1000);

    $site_object = new SiteObject();
    $site_object->setTitle('test');
    $site_object->setLocaleId('fr');
    $site_object->attachBehaviour($this->behaviour);
    $this->behaviour->setReturnValue('getId', 25);

    $this->behaviour_mapper->expectOnce('save', array(new IsAExpectation('MockSiteObjectBehaviour')));
    $mapper->setReturnReference('_getBehaviourMapper', $this->behaviour_mapper);

    $id = $mapper->insert($site_object);

    $this->assertEqual($site_object->getSiteObjectId(), $id);

    $this->_checkSysSiteObjectRecord($site_object);

    $mapper->tally();
  }

  function testUpdateSiteObjectRecordFailedNoId()
  {
    $mapper = new SiteObjectMapper();
    $site_object = new SiteObject();

    $mapper->update($site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'site object id not set');
  }

  function  testUpdateSiteObjectRecordFailedNoBehaviourId()
  {
    $mapper = new SiteObjectMapper();
    $site_object = new SiteObject();
    $site_object->setSiteObjectId(1);

    $mapper->update($site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'behaviour id not attached');
  }

  function testUpdateSiteObjectRecordOk()
  {
    $old_data = array('id' => $id = 1,
                      'class_id' => $class_id = 1000,
                      'behaviour_id' => $behaviour_id = 10,
                      'locale_id' => $locale_id = 'ru',
                      'creator_id' => $creator_id = 100,
                      'title' => $title = 'title',
                      'modified_date' => $modified_date = time() - 5,
                      'created_date' => $created_date = time() - 10);

    $this->db->insert('sys_site_object', $old_data);

    $mapper = new SiteObjectMapperTestVersion($this);

    $site_object = new SiteObject();
    $site_object->setSiteObjectId($id);
    $site_object->setClassId($class_id);
    $site_object->setCreatorId($creator_id);
    $site_object->setCreatedDate($created_date);
    $site_object->setTitle('test2');
    $site_object->setLocaleId('fr');
    $site_object->attachBehaviour($this->behaviour);
    $this->behaviour->setReturnValue('getId', $new_behaviour_id = 25);

    $this->behaviour_mapper->expectOnce('save', array(new IsAExpectation('MockSiteObjectBehaviour')));

    $mapper->setReturnReference('_getBehaviourMapper', $this->behaviour_mapper);

    $mapper->update($site_object);

    $this->assertTrue($site_object->getModifiedDate() > $modified_date);

    $this->_checkSysSiteObjectRecord($site_object);
  }

  function testCantDeleteNoId()
  {
    $site_object = new SiteObject();
    $mapper = new SiteObjectMapper();

    $mapper->delete($site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'site object id not set');
  }

  function testDelete()
  {
    $site_object = new SiteObject();
    $mapper = new SiteObjectMapper();

    $this->db->insert('sys_site_object', array('id' => $object_id = 1));

    $site_object->setSiteObjectId($object_id);

    $mapper->delete($site_object);

    $rs = $this->db->select('sys_site_object', '*', array('id' => $object_id));
    $this->assertTrue(!$record = $rs->getRow());
  }

  function _checkSysSiteObjectRecord($site_object)
  {
    $rs =& $this->db->select('sys_site_object', '*', array('id' => $site_object->getSiteObjectId()));

    $record = $rs->getRow();

    $this->assertNotNull($site_object->getTitle());
    $this->assertEqual($record['title'], $site_object->getTitle());

    $this->assertNotNull($site_object->getLocaleId());
    $this->assertEqual($record['locale_id'], $site_object->getLocaleId());

    $this->assertFalse(!$record['class_id']);//???

    $this->assertNotNull($site_object->getCreatorId());
    $this->assertEqual($record['creator_id'], $site_object->getCreatorId());

    $bhv =& $site_object->getBehaviour();
    $this->assertNotNull($bhv->getId());
    $this->assertEqual($record['behaviour_id'], $bhv->getId());

    $this->assertNotNull($site_object->getCreatedDate());
    $this->assertEqual($record['created_date'], $site_object->getCreatedDate());

    $this->assertNotNull($site_object->getModifiedDate());
    $this->assertEqual($record['modified_date'], $site_object->getModifiedDate());
  }
}

?>