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
require_once(LIMB_DIR . '/class/db/LimbDbPool.class.php');
require_once(LIMB_DIR . '/class/data_mappers/VersionedOneTableObjectsMapper.class.php');
require_once(LIMB_DIR . '/class/site_objects/VersionedSiteObject.class.php');
require_once(dirname(__FILE__) . '/OneTableObjectsMapperTestVersionDbTable.class.php');
require_once(LIMB_DIR . '/class/finders/VersionedOneTableObjectsRawFinder.class.php');
require_once(LIMB_DIR . '/class/permissions/User.class.php');
require_once(LIMB_DIR . '/class/LimbBaseToolkit.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                      'VersionedOneTableObjectsToolkitMock', array());

class VersionedOneTableObjectsTestToolkitMock extends VersionedOneTableObjectsToolkitMock
{
  var $_mocked_methods = array('getUser');

  function getUser()
  {
    $args = func_get_args();
    return $this->_mock->_invoke('getUser', $args);
  }
}

class VersionedOneTableObjectsMapperTestVersion extends VersionedOneTableObjectsMapper
{
  function _createDomainObject()
  {
    return new VersionedOneTableObjectsMapperTestNewsObject();
  }

  function _defineDbTableName()
  {
    return 'OneTableObjectsMapperTestVersion';
  }
}

Mock :: generatePartial('VersionedOneTableObjectsMapperTestVersion',
                        'VersionedOneTableObjectsMapperTestVersionMock',
                        array('_doParentInsert',
                              '_doParentUpdate',
                              '_doParentDelete',
                              '_getFinder',
                              '_doLoad'));
Mock :: generate('User');
Mock :: generate('VersionedOneTableObjectsRawFinder');

class VersionedOneTableObjectsMapperTestNewsObject extends VersionedSiteObject
{
  function getAnnotation()
  {
    return $this->get('annotation');
  }

  function setAnnotation($annotation)
  {
    $this->set('annotation', $annotation);
  }

  function getContent()
  {
    return $this->get('content');
  }

  function setContent($content)
  {
    $this->set('content', $content);
  }

  function getNewsDate()
  {
    return $this->get('news_date');
  }

  function setNewsDate($news_date)
  {
    $this->set('news_date', $news_date);
  }
}

class VersionedOneTableObjectsMapperTest extends LimbTestCase
{
  var $db;
  var $mapper;
  var $toolkit;

  function VersionedOneTableObjectsMapperTest()
  {
    parent :: LimbTestCase('versioned one table object mapper test');
  }

  function setUp()
  {
    $this->db =& LimbDbPool :: getConnection();

    $this->_cleanUp();

    $this->user = new MockUser($this);
    $this->user->setReturnValue('getId', 125);

    $this->toolkit = new VersionedOneTableObjectsTestToolkitMock($this);
    $this->toolkit->setReturnReference('getUser', $this->user);
    Limb :: registerToolkit($this->toolkit);

    $this->mapper = new VersionedOneTableObjectsMapperTestVersionMock($this);
  }

  function tearDown()
  {
    $this->mapper->tally();
    $this->toolkit->tally();

    Limb :: popToolkit();

    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->sqlDelete('sys_object_version');
    $this->db->sqlDelete('test_one_table_object');
  }

  function testGetDbTable()
  {
    $this->assertIsA($this->mapper->getDbTable(), 'OneTableObjectsMapperTestVersionDbTable');
  }

  function testInsert()
  {
    $site_object = new VersionedOneTableObjectsMapperTestNewsObject();
    $site_object->setId($object_id = 100);
    $site_object->setVersion(1);
    $site_object->setCreatorId(1);
    $site_object->setCreatedDate(1);
    $site_object->setModifiedDate(1);

    $this->mapper->expectOnce('_doParentInsert', array($site_object));

    $this->mapper->insert($site_object);

    $this->assertTrue($site_object->getCreatedDate() >= time());
    $this->assertTrue($site_object->getModifiedDate() >= time());
    $this->assertEqual($site_object->getCreatorId(), $this->user->getId());

    $this->_checkSysObjectVersionRecord($site_object);
  }

  function testVersionedUpdate()
  {
    $site_object = new VersionedOneTableObjectsMapperTestNewsObject();

    $site_object->setId($object_id = 100);
    $site_object->setVersion(1);
    $site_object->increaseVersion();

    $this->db->sqlInsert('test_one_table_object',
                         array('object_id' => $object_id,
                               'identifier' => 'test',
                               'title' => 'Title',
                               'annotation' => 'news annotation',
                               'content' => 'news content',
                               'news_date' => '2000-01-02 00:00:00',
                               'version' => 1));

    $this->mapper->expectOnce('_doParentUpdate', array($site_object));

    $site_object->setIdentifier('test2');
    $site_object->setTitle('Title2');
    $site_object->setAnnotation('news annotation2');
    $site_object->setContent('news content2');
    $site_object->setNewsDate('2004-01-02 00:00:00');

    $this->mapper->update($site_object);

    $this->assertEqual($site_object->getVersion(), 2);

    $this->db->sqlSelect('test_one_table_object');
    $this->assertEqual(sizeof($this->db->getArray()), 2);

    $this->_checkLinkedTableRecord($site_object);
    $this->_checkSysObjectVersionRecord($site_object);
  }

  function testNonversionedUpdateOk()
  {
    $site_object = new VersionedOneTableObjectsMapperTestNewsObject();

    $site_object->setId($object_id = 100);
    $site_object->setVersion($version = 1);
    $site_object->setCreatorId($creator_id = 100);
    $site_object->setCreatedDate($created_date = 10);
    $site_object->setModifiedDate($modified_date = 10);

    $this->db->sqlInsert('sys_object_version',
                         array('object_id' => $object_id,
                               'creator_id' => $creator_id,
                               'created_date' => $created_date,
                               'modified_date' => $modified_date,
                               'version' => $version));


    $this->mapper->expectOnce('_doParentUpdate', array($site_object));

    $this->mapper->update($site_object);

    $this->assertEqual($site_object->getCreatedDate(), $created_date);
    $this->assertTrue($site_object->getModifiedDate() >= time() - 60);

    $this->_checkSysObjectVersionRecord($site_object);
  }

  function testFindByVersionFailed()
  {
    $finder = new MockVersionedOneTableObjectsRawFinder($this);
    $finder->setReturnValue('findByVersion', array(), array($object_id  = 10, $version = 10000));

    $this->mapper->setReturnReference('_getFinder', $finder);

    $this->assertNull($this->mapper->findByVersion($object_id, $version));
  }

  function testFindByVersionOk()
  {
    $finder = new MockVersionedOneTableObjectsRawFinder($this);

    $result = array('id' => $id = 10,
                    'identifier' => $identifier = 'test',
                    'current_version' => 4,
                    'version' => $version = 3,
                    'behaviour_id' => $behaviour_id = 100);

    $finder->setReturnValue('findByVersion', $result, array($id, $version));

    $this->mapper->setReturnReference('_getFinder', $finder);
    $this->mapper->expectOnce('_doLoad', array($result, new IsAExpectation('VersionedOneTableObjectsMapperTestNewsObject')));
    $this->mapper->findByVersion($id, $version);
  }

  function testDelete()
  {
    $site_object = new VersionedOneTableObjectsMapperTestNewsObject();

    $site_object->setId($object_id = 100);

    $this->db->sqlInsert('sys_object_version',
                         array('object_id' => $object_id,
                               'version' => 1));

    $this->db->sqlInsert('sys_object_version',
                         array('object_id' => $object_id,
                               'version' => 2));

    $this->db->sqlInsert('sys_object_version',
                         array('object_id' => $junk_object_id = 101,
                               'version' => 2));


    $this->mapper->expectOnce('_doParentDelete', array($site_object));
    $this->mapper->delete($site_object);

    $this->db->sqlSelect('sys_object_version');
    $array = $this->db->getArray();

    $this->assertEqual(sizeof($array), 1);
    $this->assertEqual($array[0]['object_id'], $junk_object_id);
  }

  function testTrimVersions()
  {
    $this->db->sqlInsert('sys_object_version',
                         array('object_id' => $object_id = 100,
                               'version' => 1));

    $this->db->sqlInsert('sys_object_version',
                         array('object_id' => $object_id,
                               'version' => 3));

    // should not be deleted!
    $this->db->sqlInsert('sys_object_version',
                         array('object_id' => $junk_object_id = 101,
                               'version' => 2));

    $this->db->sqlInsert('test_one_table_object',
                         array('object_id' => $object_id,
                               'identifier' => 'test',
                               'title' => 'Title',
                               'annotation' => 'news annotation',
                               'content' => 'news content',
                               'news_date' => '2000-01-02 00:00:00',
                               'version' => 1));

    // should not be deleted!
    $this->db->sqlInsert('test_one_table_object',
                         array('object_id' => $junk_object_id,
                               'identifier' => 'test',
                               'title' => 'Title',
                               'annotation' => 'news annotation',
                               'content' => 'news content',
                               'news_date' => '2000-01-02 00:00:00',
                               'version' => 1));
    $this->mapper->trimVersions($object_id, $version = 2);

    $this->db->sqlSelect('sys_object_version');
    $array = $this->db->getArray();

    $this->assertEqual(sizeof($array), 1);
    $this->assertEqual($array[0]['object_id'], $junk_object_id);

    $this->db->sqlSelect('test_one_table_object');
    $array = $this->db->getArray();

    $this->assertEqual(sizeof($array), 1);
    $this->assertEqual($array[0]['object_id'], $junk_object_id);
  }

  function _checkSysObjectVersionRecord($site_object)
  {
    $conditions['object_id'] = $site_object->getId();
    $conditions['version'] = $site_object->getVersion();

    $this->db->sqlSelect('sys_object_version', '*', $conditions);
    $record = $this->db->fetchRow();

    $this->assertEqual($record['object_id'], $site_object->getId());
    $this->assertEqual($record['version'], $site_object->getVersion());
    $this->assertEqual($record['creator_id'], $site_object->getCreatorId());
    $this->assertEqual($record['created_date'], $site_object->getCreatedDate());
    $this->assertEqual($record['modified_date'], $site_object->getModifiedDate());
  }

  function _checkLinkedTableRecord($site_object)
  {
    $conditions['object_id'] = $site_object->getId();
    $conditions['version'] = $site_object->getVersion();

    $db_table = $this->mapper->getDbTable();
    $arr = $db_table->getList($conditions, 'id');

    $this->assertEqual(sizeof($arr), 1);
    $record = current($arr);

    $this->assertEqual($record['identifier'], $site_object->getIdentifier());
    $this->assertEqual($record['title'], $site_object->getTitle());
    $this->assertEqual($record['annotation'], $site_object->getAnnotation());
    $this->assertEqual($record['content'], $site_object->getContent());
    $this->assertEqual($record['news_date'], $site_object->getNewsDate());
  }
}

?>