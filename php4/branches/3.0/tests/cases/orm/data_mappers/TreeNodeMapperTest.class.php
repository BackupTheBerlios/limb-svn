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
require_once(LIMB_DIR . '/core/Object.class.php');
require_once(LIMB_DIR . '/core/tree/Tree.interface.php');
require_once(LIMB_DIR . '/core/data_mappers/ObjectIdentifierGenerator.interface.php');

Mock :: generate('Tree');
Mock :: generatePartial('LimbBaseToolkit',
                      'TreeNodeDataMapperTestToolkit',
                      array('getTree'));

Mock :: generatePartial('TreeNodeDataMapper',
                        'TreeNodeDataMapperTestVersion',
                        array('_getIdentifierGenerator'));
Mock :: generate('ObjectIdentifierGenerator', 'MockIdentifierGenerator');

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
    Limb :: restoreToolkit();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_tree');
    $this->db->delete('sys_uid');
  }

  function testLoad()
  {
    $mapper = new TreeNodeDataMapper();
    $object = new Object();

    $record = new Dataspace();
    $record->import(array('node_id' => $node_id = 10,
                          'parent_node_id' => $parent_node_id = 100,
                          'identifier' => $identifier = 'test',
                          ));

    $mapper->load($record, $object);

    $this->assertEqual($object->get('node_id'), $node_id);
    $this->assertEqual($object->get('parent_node_id'), $parent_node_id);
    $this->assertEqual($object->get('identifier'), $identifier);
  }

  function testFailedInsertTreeNodeParentIdNotSet()
  {
    $mapper = new TreeNodeDataMapper();

    $object = new Object();

    $mapper->insert($object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'tree parent node is empty');
  }

  function testFailedInsertTreeNodeCantRegisterNode()
  {
    $mapper = new TreeNodeDataMapper();

    $object = new Object();
    $object->set('parent_node_id', $parent_node_id = 10);

    $this->tree->expectOnce('canAddNode', array($parent_node_id));
    $this->tree->setReturnValue('canAddNode', false);

    $mapper->insert($object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'tree registering failed');
    $this->assertEqual($e->getAdditionalParams(), array('parent_node_id' => 10));
  }

  function testFailedInsertCantGenerateIdentifier()
  {
    $mapper = new TreeNodeDataMapperTestVersion($this);
    $generator = new MockIdentifierGenerator($this);

    $mapper->setReturnReference('_getIdentifierGenerator', $generator);

    $object = new Object();
    $object->set('parent_node_id', $parent_node_id = 10);

    $this->tree->expectOnce('canAddNode', array($parent_node_id));
    $this->tree->setReturnValue('canAddNode', true);

    $generator->expectOnce('generate', array($object));
    $generator->setReturnValue('generate', false, array($object));

    $mapper->insert($object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'failed to generate identifier');

    $generator->tally();
  }

  function testFailedInsertCantMakeTreeNode()
  {
    $mapper = new TreeNodeDataMapperTestVersion($this);
    $generator = new MockIdentifierGenerator($this);

    $mapper->setReturnReference('_getIdentifierGenerator', $generator);

    $object = new Object();
    $object->set('parent_node_id', $parent_node_id = 10);

    $this->tree->expectOnce('canAddNode', array($parent_node_id));
    $this->tree->setReturnValue('canAddNode', true);

    $generator->expectOnce('generate', array($object));
    $generator->setReturnValue('generate', $identifier = 'identifier', array($object));

    $expected = array($parent_node_id, array('identifier' => $identifier));
    $this->tree->expectOnce('createSubNode', $expected);
    $this->tree->setReturnValue('createSubNode', false, $expected);

    $mapper->insert($object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'could not create tree node');

    $generator->tally();
  }

  function testInsertTreeNodeOk()
  {
    $mapper = new TreeNodeDataMapperTestVersion($this);
    $generator = new MockIdentifierGenerator($this);

    $mapper->setReturnReference('_getIdentifierGenerator', $generator);

    $object = new Object();
    $object->set('parent_node_id', $parent_node_id = 10);

    $this->tree->expectOnce('canAddNode', array($parent_node_id));
    $this->tree->setReturnValue('canAddNode', true);

    $generator->expectOnce('generate', array($object));
    $generator->setReturnValue('generate', $identifier = 'identifier', array($object));

    $expected = array($parent_node_id, array('identifier' => $identifier));
    $this->tree->expectOnce('createSubNode', $expected);
    $this->tree->setReturnValue('createSubNode', $node_id = 100, $expected);

    $mapper->insert($object);

    $this->assertEqual($object->get('node_id'), $node_id);
    $this->assertEqual($object->get('identifier'), $identifier);

    $generator->tally();
  }

  function  testUpdateTreeNodeFailedNoNodeId()
  {
    $mapper = new TreeNodeDataMapper();

    $object = new Object();

    $mapper->update($object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'node id not set');
  }

  function  testUpdateTreeNodeFailedNoParentNodeId()
  {
    $mapper = new TreeNodeDataMapper();

    $object = new Object();
    $object->set('node_id', 10);

    $mapper->update($object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'parent node id not set');
  }

  function testUpdateNoNeedToUpdate()
  {
    $mapper = new TreeNodeDataMapper();

    $object = new Object();
    $object->set('node_id', $node_id = 100);
    $object->set('parent_node_id', $parent_node_id = 10);
    $object->set('identifier', $identifier = 'test');

    $this->tree->expectOnce('getNode');
    $this->tree->setReturnValue('getNode', array('identifier' => $identifier,
                                                 'parent_id' => $parent_node_id), array($node_id));

    $this->tree->expectNever('moveTree');
    $this->tree->expectNever('updateNode');
    $mapper->update($object);
  }

  function testUpdateNoNeedToMove()
  {
    $mapper = new TreeNodeDataMapper();

    $object = new Object();
    $object->set('node_id', $node_id = 100);
    $object->set('parent_node_id', $parent_node_id = 10);
    $object->set('identifier', $identifier = 'test');

    $this->tree->expectOnce('getNode');
    $this->tree->setReturnValue('getNode', array('identifier' => 'test2',
                                                 'parent_id' => $parent_node_id), array($node_id));

    $this->tree->expectNever('moveTree');
    $this->tree->expectOnce('updateNode', array($node_id, array('identifier' => $identifier), true));
    $mapper->update($object);
  }

  function testUpdateTreeNodeFailedToMove()
  {
    $mapper = new TreeNodeDataMapper();

    $object = new Object();
    $object->set('node_id', $node_id = 100);
    $object->set('parent_node_id', $parent_node_id = 10);

    $this->tree->expectOnce('canAddNode');
    $this->tree->setReturnValue('canAddNode', true);

    $this->tree->expectOnce('getNode');
    $this->tree->setReturnValue('getNode', array('parent_id' => 110), array($node_id));

    $this->tree->expectOnce('moveTree');
    $this->tree->setReturnValue('moveTree', false, array($node_id, $parent_node_id));

    $mapper->update($object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'could not move node');
  }

  function testUpdateCantChangeParent()
  {
    $mapper = new TreeNodeDataMapper();

    $object = new Object();
    $object->set('node_id', $node_id = 100);
    $object->set('parent_node_id', $parent_node_id = 10);
    $object->set('identifier', $identifier = 'test');

    $this->tree->expectOnce('getNode');
    $this->tree->setReturnValue('getNode', array('identifier' => $identifier,
                                                 'parent_id' => 11), array($node_id));

    $this->tree->expectOnce('canAddNode');
    $this->tree->setReturnValue('canAddNode', false);

    $this->tree->expectNever('moveTree');
    $this->tree->expectNever('updateNode');
    $mapper->update($object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'new parent cant accept children');
  }

  function testUpdateTreeMovedOK()
  {
    $mapper = new TreeNodeDataMapper();

    $object = new Object();
    $object->set('node_id', $node_id = 100);
    $object->set('parent_node_id', $parent_node_id = 10);
    $object->set('identifier', $identifier = 'test');

    $this->tree->expectOnce('canAddNode');
    $this->tree->setReturnValue('canAddNode', true);

    $this->tree->expectOnce('getNode');
    $this->tree->setReturnValue('getNode', array('identifier' => $identifier,
                                                 'parent_id' => 110), array($node_id));

    $this->tree->expectOnce('moveTree');
    $this->tree->setReturnValue('moveTree', true, array($node_id, $parent_node_id));

    $mapper->update($object);
  }

  function testCantDeleteNoNodeId()
  {
    $mapper = new TreeNodeDataMapper();
    $object = new Object();

    $mapper->delete($object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'node id not set');
  }

  function testCantDeleteFromTree()
  {
    $mapper = new TreeNodeDataMapper();
    $object = new Object();

    $object->set('node_id', $node_id = 100);

    $this->tree->expectOnce('canDeleteNode', array($node_id));
    $this->tree->setReturnValue('canDeleteNode', false, array($node_id));

    $mapper->delete($object);
  }

  function testDelete()
  {
    $mapper = new TreeNodeDataMapper();
    $object = new Object();

    $object->set('node_id', $node_id = 100);

    $this->tree->setReturnValue('canDeleteNode', true, array($node_id));
    $this->tree->expectOnce('deleteNode', array($node_id));

    $mapper->delete($object);
  }
}

?>