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
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');
require_once(LIMB_DIR . '/core/data_mappers/TreeNodeDataMapper.class.php');
require_once(LIMB_DIR . '/core/site_objects/SiteObject.class.php');
require_once(LIMB_DIR . '/core/tree/Tree.interface.php');
require_once(LIMB_DIR . '/core/data_mappers/SiteObjectIdentifierGenerator.interface.php');

Mock :: generate('Tree');
Mock :: generatePartial('LimbBaseToolkit',
                      'TreeNodeDataMapperTestToolkit',
                      array('getTree'));

Mock :: generatePartial('TreeNodeDataMapper',
                        'TreeNodeDataMapperTestVersion',
                        array('_getIdentifierGenerator'));
Mock :: generate('SiteObjectIdentifierGenerator', 'MockIdentifierGenerator');

class TreeNodeDataMapperTest extends LimbTestCase
{
  var $db;
  var $toolkit;
  var $tree;

  function TreeNodeDataMapperTest()
  {
    parent :: LimbTestCase('tree node mapper test');
  }

  function setUp()
  {
    $this->tree = new MockTree($this);
    $this->toolkit = new TreeNodeDataMapperTestToolkit($this);

    $this->toolkit->setReturnReference('getTree', $this->tree);

    Limb :: registerToolkit($this->toolkit);

    $this->db =& new SimpleDb(LimbDbPool :: getConnection());

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->tree->tally();

    $this->_cleanUp();
    Limb :: popToolkit();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_site_object_tree');
  }

  function testFailedInsertTreeNodeParentIdNotSet()
  {
    $mapper = new TreeNodeDataMapper();

    $site_object = new SiteObject();

    $mapper->insert($site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'tree parent node is empty');
  }

  function testFailedInsertTreeNodeCantRegisterNode()
  {
    $mapper = new TreeNodeDataMapper();

    $site_object = new SiteObject();
    $site_object->setParentNodeId($parent_node_id = 10);

    $this->tree->expectOnce('canAddNode', array($parent_node_id));
    $this->tree->setReturnValue('canAddNode', false);

    $mapper->insert($site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'tree registering failed');
    $this->assertEqual($e->getAdditionalParams(), array('parent_node_id' => 10));
  }

  function testFailedInsertCantGenerateIdentifier()
  {
    $mapper = new TreeNodeDataMapperTestVersion($this);
    $generator = new MockIdentifierGenerator($this);

    $mapper->setReturnReference('_getIdentifierGenerator', $generator);

    $site_object = new SiteObject();
    $site_object->setParentNodeId($parent_node_id = 10);

    $this->tree->expectOnce('canAddNode', array($parent_node_id));
    $this->tree->setReturnValue('canAddNode', true);

    $generator->expectOnce('generate', array($site_object));
    $generator->setReturnValue('generate', false, array($site_object));

    $mapper->insert($site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'failed to generate identifier');

    $generator->tally();
  }

  function testFailedInsertCantMakeTreeNode()
  {
    $mapper = new TreeNodeDataMapperTestVersion($this);
    $generator = new MockIdentifierGenerator($this);

    $mapper->setReturnReference('_getIdentifierGenerator', $generator);

    $site_object = new SiteObject();
    $site_object->setParentNodeId($parent_node_id = 10);

    $this->tree->expectOnce('canAddNode', array($parent_node_id));
    $this->tree->setReturnValue('canAddNode', true);

    $generator->expectOnce('generate', array($site_object));
    $generator->setReturnValue('generate', $identifier = 'identifier', array($site_object));

    $expected = array($parent_node_id, array('identifier' => $identifier));
    $this->tree->expectOnce('createSubNode', $expected);
    $this->tree->setReturnValue('createSubNode', false, $expected);

    $mapper->insert($site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'could not create tree node');

    $generator->tally();
  }

  function testInsertTreeNodeOk()
  {
    $mapper = new TreeNodeDataMapperTestVersion($this);
    $generator = new MockIdentifierGenerator($this);

    $mapper->setReturnReference('_getIdentifierGenerator', $generator);

    $site_object = new SiteObject();
    $site_object->setParentNodeId($parent_node_id = 10);

    $this->tree->expectOnce('canAddNode', array($parent_node_id));
    $this->tree->setReturnValue('canAddNode', true);

    $generator->expectOnce('generate', array($site_object));
    $generator->setReturnValue('generate', $identifier = 'identifier', array($site_object));

    $expected = array($parent_node_id, array('identifier' => $identifier));
    $this->tree->expectOnce('createSubNode', $expected);
    $this->tree->setReturnValue('createSubNode', $node_id = 100, $expected);

    $mapper->insert($site_object);

    $this->assertEqual($site_object->getNodeId(), $node_id);
    $this->assertEqual($site_object->getIdentifier(), $identifier);

    $generator->tally();
  }

  /*function  testUpdateSiteObjectRecordFailedNoId()
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
  }*/
}

?>