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
require_once(LIMB_DIR . '/core/finders/SiteObjectsRawFinder.class.php');
require_once(LIMB_DIR . '/core/site_objects/SiteObject.class.php');
require_once(LIMB_DIR . '/core/behaviours/SiteObjectBehaviour.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/tree/TreeDecorator.class.php');
require_once(LIMB_DIR . '/core/permissions/User.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                      'SiteObjectManipulationTestToolkit',
                      array('getTree', 'getUser', 'constant', 'createDataMapper'));

Mock :: generate('TreeDecorator');
Mock :: generate('User');
Mock :: generate('SiteObject');
Mock :: generate('SiteObjectsRawFinder');
Mock :: generate('SiteObjectBehaviour');
Mock :: generate('SiteObjectBehaviourMapper');

Mock :: generatePartial('SiteObjectMapper',
                      'SiteObjectMapperTestVersion0',
                      array('insert',
                            'update',
                            '_getFinder',
                            '_getBehaviourMapper'));

Mock :: generatePartial('SiteObjectMapper',
                      'SiteObjectMapperTestVersion1',
                      array('_insertTreeNode',
                            '_updateTreeNode',
                            '_canDeleteSiteObjectRecord',
                            'getClassId'));

Mock :: generatePartial('SiteObjectMapper',
                      'SiteObjectMapperTestVersion2',
                      array('_canAddNodeToParent',
                            '_insertSiteObjectRecord',
                            '_updateSiteObjectRecord'));


class SiteObjectMapperTest extends LimbTestCase
{
  var $db;
  var $behaviour;
  var $behaviour_mapper;
  var $site_object;
  var $toolkit;
  var $tree;
  var $user;

  function SiteObjectMapperTest()
  {
    parent :: LimbTestCase('site object mapper test');
  }

  function setUp()
  {
    $this->toolkit = new SiteObjectManipulationTestToolkit($this);
    $this->tree = new MockTreeDecorator($this);
    $this->user = new MockUser($this);
    $this->behaviour_mapper = new MockSiteObjectBehaviourMapper($this);
    $this->site_object = new MockSiteObject($this);
    $this->user->setReturnValue('getId', 125);

    $this->toolkit->setReturnReference('getTree', $this->tree);
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
    $this->tree->tally();
    $this->site_object->tally();
    $this->behaviour->tally();
    $this->behaviour_mapper->tally();

    Limb :: popToolkit();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_site_object');
    $this->db->delete('sys_site_object_tree');
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

  function testGetParentLocaleIdDefault()
  {
    $mapper = new SiteObjectMapper();

    $this->toolkit->setReturnValue('constant',
                                   $locale_id  = 'ge',
                                   array('DEFAULT_CONTENT_LOCALE_ID'));

    $this->assertEqual($mapper->getParentLocaleId(10000), $locale_id);
  }

  function testGetParentLocaleId()
  {
    $this->db->insert('sys_site_object', array('locale_id' => $locale_id = 'ru',
                                                  'id' => 200));

    $this->db->insert('sys_site_object_tree', array('object_id' => 200,
                                                        'id' => $parent_node_id = 300));

    $mapper = new SiteObjectMapper();

    $this->assertEqual($mapper->getParentLocaleId($parent_node_id), $locale_id);
  }

  function testFindById()
  {
    $finder = new MockSiteObjectsRawFinder($this);
    $result = array('id' => $id = 10,
                    'identifier' => $identifier = 'test',
                    'behaviour_id' => $behaviour_id = 100);

    $finder->expectOnce('findById', array($id));
    $finder->setReturnValue('findById', $result, array($id));

    $mapper = new SiteObjectMapperTestVersion0($this);

    $mapper->setReturnReference('_getFinder', $finder);
    $mapper->setReturnReference('_getBehaviourMapper', $this->behaviour_mapper);

    $this->behaviour_mapper->expectOnce('findById', array($behaviour_id));
    $this->behaviour_mapper->setReturnReference('findById', $this->behaviour, array($behaviour_id));

    $site_object = $mapper->findById($id);

    $this->assertEqual($site_object->getId(), $id);
    $this->assertEqual($site_object->getIdentifier(), $identifier);

    $this->assertIsA($site_object->getBehaviour(), get_class($this->behaviour));

    $finder->tally();
    $mapper->tally();
  }

  function testFailedInsertSiteObjectRecordNoIdentifier()
  {
    $mapper = new SiteObjectMapperTestVersion1($this);
    $mapper->setReturnValue('getClassId', 1000);

    $this->site_object->expectOnce('getIdentifier');
    $this->site_object->setReturnValue('getIdentifier', null);

    $mapper->insert($this->site_object);
    $this->assertTrue(catch('Exception', $e));
    $mapper->tally();
  }

  function testFailedInsertSiteObjectRecordNoBehaviourAttached()
  {
    $mapper = new SiteObjectMapperTestVersion1($this);
    $mapper->setReturnValue('getClassId', 1000);

    $this->site_object->setReturnValue('getIdentifier', 'test');
    $this->site_object->expectOnce('getBehaviour');
    $this->site_object->setReturnValue('getBehaviour', null);

    $mapper->insert($this->site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'behaviour is not attached');

    $mapper->tally();
  }

  function testInsertSiteObjectRecordOk()
  {
    $mapper = new SiteObjectMapperTestVersion1($this);
    $mapper->setReturnValue('getClassId', 1000);
    $mapper->expectOnce('_insertTreeNode');
    $mapper->setReturnValue('_insertTreeNode', $node_id = 120);

    $site_object = new SiteObject();
    $site_object->setIdentifier('test');
    $site_object->setTitle('test');
    $site_object->setLocaleId('fr');
    $site_object->attachBehaviour($this->behaviour);
    $this->behaviour->setReturnValue('getId', 25);

    $this->behaviour_mapper->expectOnce('save', array(new IsAExpectation('MockSiteObjectBehaviour')));

    $id = $mapper->insert($site_object);

    $this->assertEqual($site_object->getId(), $id);
    $this->assertEqual($site_object->getNodeId(), $node_id);

    $this->_checkSysSiteObjectRecord($site_object);

    $mapper->tally();
  }

  function testFailedInsertTreeNodeParentIdNotSet()
  {
    $mapper = new SiteObjectMapperTestVersion2($this);

    $mapper->expectOnce('_insertSiteObjectRecord');
    $mapper->setReturnValue('_insertSiteObjectRecord', $object_id = 120);

    $this->site_object->setReturnValue('getId', $object_id);

    $mapper->insert($this->site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'tree parent node is empty');

    $mapper->tally();
  }

  function testFailedInsertTreeNodeCantRegisterNode()
  {
    $mapper = new SiteObjectMapperTestVersion2($this);

    $mapper->expectOnce('_insertSiteObjectRecord');
    $mapper->setReturnValue('_insertSiteObjectRecord', $object_id = 120);

    $this->site_object->setReturnValue('getId', $object_id);
    $this->site_object->setReturnValue('getParentNodeId', $parent_node_id = 10);

    $mapper->expectOnce('_canAddNodeToParent', array($parent_node_id));
    $mapper->setReturnValue('_canAddNodeToParent', false);

    $mapper->insert($this->site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'tree registering failed');
    $this->assertEqual($e->getAdditionalParams(), array('parent_node_id' => 10));
  }

  function testInsertTreeNodeOk()
  {
    $mapper = new SiteObjectMapperTestVersion2($this);

    $mapper->expectOnce('_insertSiteObjectRecord');
    $mapper->setReturnValue('_insertSiteObjectRecord', $object_id = 120);

    $this->site_object->setReturnValue('getId', $object_id);
    $this->site_object->setReturnValue('getParentNodeId', $parent_node_id = 10);

    $mapper->setReturnValue('_canAddNodeToParent', true);
    $this->tree->expectOnce('createSubNode');
    $this->tree->setReturnValue('createSubNode', $node_id = 200);

    $this->site_object->expectOnce('setId', array($object_id));
    $this->site_object->expectOnce('setNodeId', array($node_id));

    $id = $mapper->insert($this->site_object);

    $mapper->tally();
  }

  function  testUpdateSiteObjectRecordFailedNoId()
  {
    $mapper = new SiteObjectMapperTestVersion1($this);
    $this->site_object->expectOnce('getId');

    $mapper->update($this->site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'object id not set');

    $mapper->tally();
  }

  function  testUpdateSiteObjectRecordFailedNoBehaviourId()
  {
    $mapper = new SiteObjectMapperTestVersion1($this);
    $this->site_object->setReturnValue('getId', 125);
    $this->site_object->setReturnValue('getIdentifier', 'test');
    $this->site_object->expectOnce('getBehaviour');
    $this->site_object->setReturnValue('getBehaviour', null);

    $mapper->update($this->site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'behaviour id not attached');

    $mapper->tally();
  }

  function  testUpdateSiteObjectRecordFailedNoIdentifier()
  {
    $mapper = new SiteObjectMapperTestVersion1($this);
    $this->site_object->setReturnValue('getId', 125);
    $this->site_object->setReturnValue('getIdentifier', null);

    $mapper->update($this->site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'identifier is empty');

    $mapper->tally();
  }

  function testUpdateSiteObjectRecordOk()
  {
    $this->db->insert('sys_site_object',
                          array('id' => $object_id = 100,
                                'title' => 'old title',
                                'identifier' => 'old identifier',
                                'class_id' => 234));

    $mapper = new SiteObjectMapperTestVersion1($this);

    $site_object = new SiteObject();
    $site_object->setId($object_id);
    $site_object->setIdentifier('test');
    $site_object->setTitle('test');
    $site_object->setLocaleId('fr');
    $site_object->attachBehaviour($this->behaviour);
    $this->behaviour->setReturnValue('getId', 25);

    $this->behaviour_mapper->expectOnce('save', array(new IsAExpectation('MockSiteObjectBehaviour')));

    $mapper->update($site_object);

    $this->_checkSysSiteObjectRecord($site_object);

    $mapper->tally();
  }

  function  testUpdateTreeNodeFailedNoNodeId()
  {
    $mapper = new SiteObjectMapperTestVersion2($this);

    $mapper->update($this->site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'node id not set');
  }

  function  testUpdateTreeNodeFailedNoParentNodeId()
  {
    $mapper = new SiteObjectMapperTestVersion2($this);

    $this->site_object->setReturnValue('getNodeId', 10);

    $mapper->update($this->site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'parent node id not set');
  }

  function  testUpdateTreeNodeFailedToMove()
  {
    $this->site_object->setReturnValue('getNodeId', $node_id = 100);
    $this->site_object->setReturnValue('getParentNodeId', $parent_node_id = 10);

    $mapper = new SiteObjectMapperTestVersion2($this);
    $mapper->setReturnValue('_canAddNodeToParent', true, array($parent_node_id));

    $this->tree->expectOnce('getNode');
    $this->tree->setReturnValue('getNode', array('parent_id' => 110), array($node_id));

    $this->tree->expectOnce('moveTree');
    $this->tree->setReturnValue('moveTree', false, array($node_id, $parent_node_id));

    $mapper->update($this->site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'could not move node');

    $mapper->tally();
  }

  function  testUpdateTreeNodeFailedNewParentCantAcceptChildren()
  {
    $this->site_object->setReturnValue('getNodeId', $node_id = 100);
    $this->site_object->setReturnValue('getParentNodeId', $parent_node_id = 10);

    $mapper = new SiteObjectMapperTestVersion2($this);
    $mapper->setReturnValue('_canAddNodeToParent', false, array($parent_node_id));

    $this->tree->expectOnce('getNode');
    $this->tree->setReturnValue('getNode', array('parent_id' => 110), array($node_id));

    $this->tree->expectNever('moveTree');

    $mapper->update($this->site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'new parent cant accept children');
  }

  function  testUpdateOkObjectNotMovedIdentifierChangedInTree()
  {
    $this->site_object->setReturnValue('getNodeId', $node_id = 100);
    $this->site_object->setReturnValue('getParentNodeId', $parent_node_id = 10);
    $this->site_object->setReturnValue('getIdentifier', $identifier = 'test');

    $mapper = new SiteObjectMapperTestVersion2($this);
    $mapper->setReturnValue('_canAddNodeToParent', true, array($parent_node_id));

    $this->tree->expectOnce('getNode');
    $this->tree->setReturnValue('getNode',
                                array('identifier' => 'test2', 'parent_id' => $parent_node_id),
                                array($node_id));

    $this->tree->expectNever('moveTree');

    $this->tree->expectOnce('updateNode', array($node_id,
                                                 array('identifier' => $identifier),
                                                 true));

    $mapper->update($this->site_object);
  }

  function  testUpdateOkObjectNotMovedIdentifierNotChangedInTree()
  {
    $this->site_object->setReturnValue('getNodeId', $node_id = 100);
    $this->site_object->setReturnValue('getParentNodeId', $parent_node_id = 10);
    $this->site_object->setReturnValue('getIdentifier', $identifier = 'test');

    $mapper = new SiteObjectMapperTestVersion2($this);
    $mapper->setReturnValue('_canAddNodeToParent', true, array($parent_node_id));

    $this->tree->expectOnce('getNode');
    $this->tree->setReturnValue('getNode',
                                array('identifier' => $identifier, 'parent_id' => $parent_node_id),
                                array($node_id));

    $this->tree->expectNever('moveTree');
    $this->tree->expectNever('updateNode');

    $mapper->update($this->site_object);
  }

  function testCantDeleteNoId()
  {
    $mapper = new SiteObjectMapper();

    $mapper->canDelete($this->site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'object id not set');
  }

  function testCantDeleteNoNodeId()
  {
    $mapper = new SiteObjectMapper();
    $this->site_object->setReturnValue('getId', 10);

    $mapper->canDelete($this->site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'node id not set');
  }

  function testCantDelete()
  {
    $this->site_object->setReturnValue('getId', 10);
    $this->site_object->setReturnValue('getNodeId', 100);

    $mapper = new SiteObjectMapperTestVersion1($this);
    $mapper->setReturnValue('_canDeleteSiteObjectRecord', false);
    $this->assertFalse($mapper->canDelete($this->site_object));
  }

  function testCantDeleteNotTerminalNode()
  {
    $this->site_object->setReturnValue('getId', 10);
    $this->site_object->setReturnValue('getNodeId', $node_id = 100);

    $mapper = new SiteObjectMapperTestVersion1($this);
    $mapper->setReturnValue('_canDeleteSiteObjectRecord', true);

    $this->tree->expectOnce('canDeleteNode', array($node_id));
    $this->tree->setReturnValue('canDeleteNode', false, array($node_id));

    $this->assertFalse($mapper->canDelete($this->site_object));
  }

  function testCanDelete()
  {
    $this->site_object->setReturnValue('getId', 10);
    $this->site_object->setReturnValue('getNodeId', $node_id = 100);

    $mapper = new SiteObjectMapperTestVersion1($this);
    $mapper->setReturnValue('_canDeleteSiteObjectRecord', true);
    $this->tree->setReturnValue('canDeleteNode', true, array($node_id));

    $this->assertTrue($mapper->canDelete($this->site_object));
  }

  function testDelete()
  {
    $this->db->insert('sys_site_object', array('id' => $object_id = 1));

    $this->site_object->setReturnValue('getId', $object_id);
    $this->site_object->setReturnValue('getNodeId', $node_id = 100);

    $mapper = new SiteObjectMapperTestVersion1($this);
    $mapper->setReturnValue('_canDeleteSiteObjectRecord', true);
    $this->tree->setReturnValue('canDeleteNode', true, array($node_id));

    $this->tree->expectOnce('deleteNode', array($node_id));

    $mapper->delete($this->site_object);

    $rs = $this->db->select('sys_site_object', '*', 'id=' . $object_id);
    $this->assertTrue(!$record = $rs->getRow());
  }

  function _checkSysSiteObjectRecord($site_object)
  {
    $rs =& $this->db->select('sys_site_object', '*', 'id=' . $site_object->getId());

    $record = $rs->getRow();

    $this->assertNotNull($site_object->getIdentifier());
    $this->assertEqual($record['identifier'], $site_object->getIdentifier());

    $this->assertNotNull($site_object->getTitle());
    $this->assertEqual($record['title'], $site_object->getTitle());

    $this->assertNotNull($site_object->getVersion());
    $this->assertEqual($record['current_version'], $site_object->getVersion());

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