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

  function  testUpdateTreeNodeFailedNoNodeId()
  {
    $mapper = new TreeNodeDataMapper();

    $site_object = new SiteObject();

    $mapper->update($site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'node id not set');
  }

  function  testUpdateTreeNodeFailedNoParentNodeId()
  {
    $mapper = new TreeNodeDataMapper();

    $site_object = new SiteObject();
    $site_object->setNodeId(10);

    $mapper->update($site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'parent node id not set');
  }

  function testUpdateNoNeedToUpdate()
  {
    $mapper = new TreeNodeDataMapper();

    $site_object = new SiteObject();
    $site_object->setNodeId($node_id = 100);
    $site_object->setParentNodeId($parent_node_id = 10);
    $site_object->setIdentifier($identifier = 'test');

    $this->tree->expectOnce('getNode');
    $this->tree->setReturnValue('getNode', array('identifier' => $identifier,
                                                 'parent_id' => $parent_node_id), array($node_id));

    $this->tree->expectNever('moveTree');
    $this->tree->expectNever('updateNode');
    $mapper->update($site_object);
  }

  function testUpdateNoNeedToMove()
  {
    $mapper = new TreeNodeDataMapper();

    $site_object = new SiteObject();
    $site_object->setNodeId($node_id = 100);
    $site_object->setParentNodeId($parent_node_id = 10);
    $site_object->setIdentifier($identifier = 'test');

    $this->tree->expectOnce('getNode');
    $this->tree->setReturnValue('getNode', array('identifier' => 'test2',
                                                 'parent_id' => $parent_node_id), array($node_id));

    $this->tree->expectNever('moveTree');
    $this->tree->expectOnce('updateNode', array($node_id, array('identifier' => $identifier), true));
    $mapper->update($site_object);
  }

  function testUpdateTreeNodeFailedToMove()
  {
    $mapper = new TreeNodeDataMapper();

    $site_object = new SiteObject();
    $site_object->setNodeId($node_id = 100);
    $site_object->setParentNodeId($parent_node_id = 10);

    $this->tree->expectOnce('canAddNode');
    $this->tree->setReturnValue('canAddNode', true);

    $this->tree->expectOnce('getNode');
    $this->tree->setReturnValue('getNode', array('parent_id' => 110), array($node_id));

    $this->tree->expectOnce('moveTree');
    $this->tree->setReturnValue('moveTree', false, array($node_id, $parent_node_id));

    $mapper->update($site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'could not move node');
  }

  function testUpdateCantChangeParent()
  {
    $mapper = new TreeNodeDataMapper();

    $site_object = new SiteObject();
    $site_object->setNodeId($node_id = 100);
    $site_object->setParentNodeId($parent_node_id = 10);
    $site_object->setIdentifier($identifier = 'test');

    $this->tree->expectOnce('getNode');
    $this->tree->setReturnValue('getNode', array('identifier' => $identifier,
                                                 'parent_id' => 11), array($node_id));

    $this->tree->expectOnce('canAddNode');
    $this->tree->setReturnValue('canAddNode', false);

    $this->tree->expectNever('moveTree');
    $this->tree->expectNever('updateNode');
    $mapper->update($site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'new parent cant accept children');
  }

  function testUpdateTreeMovedOK()
  {
    $mapper = new TreeNodeDataMapper();

    $site_object = new SiteObject();
    $site_object->setNodeId($node_id = 100);
    $site_object->setParentNodeId($parent_node_id = 10);
    $site_object->setIdentifier($identifier = 'test');

    $this->tree->expectOnce('canAddNode');
    $this->tree->setReturnValue('canAddNode', true);

    $this->tree->expectOnce('getNode');
    $this->tree->setReturnValue('getNode', array('identifier' => $identifier,
                                                 'parent_id' => 110), array($node_id));

    $this->tree->expectOnce('moveTree');
    $this->tree->setReturnValue('moveTree', true, array($node_id, $parent_node_id));

    $mapper->update($site_object);
  }

  function testCantDeleteNoNodeId()
  {
    $mapper = new TreeNodeDataMapper();
    $site_object = new SiteObject();

    $mapper->delete($site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'node id not set');
  }

  function testCantDeleteFromTree()
  {
    $mapper = new TreeNodeDataMapper();
    $site_object = new SiteObject();

    $site_object->setNodeId($node_id = 100);

    $this->tree->expectOnce('canDeleteNode', array($node_id));
    $this->tree->setReturnValue('canDeleteNode', false, array($node_id));

    $mapper->delete($site_object);
  }

  function testDelete()
  {
    $mapper = new TreeNodeDataMapper();
    $site_object = new SiteObject();

    $site_object->setNodeId($node_id = 100);

    $this->tree->setReturnValue('canDeleteNode', true, array($node_id));
    $this->tree->expectOnce('deleteNode', array($node_id));

    $mapper->delete($site_object);
  }

  function testLoad()
  {
    $mapper = new TreeNodeDataMapper();
    $site_object = new SiteObject();

    $record = new Dataspace();
    $record->import(array('node_id' => $node_id = 10,
                          'parent_node_id' => $parent_node_id = 100,
                          'identifier' => $identifier = 'test',
                          ));

    $mapper->load($record, $site_object);

    $this->assertEqual($site_object->getNodeId(), $node_id);
    $this->assertEqual($site_object->getParentNodeId(), $parent_node_id);
    $this->assertEqual($site_object->getIdentifier(), $identifier);

  }
}

?>