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
require_once(LIMB_DIR . '/core/tree/TreeDecorator.class.php');
require_once(LIMB_DIR . '/core/tree/Tree.interface.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');

Mock :: generate('LimbBaseToolkit', 'MockLimbToolkit');
Mock :: generate('Tree');

class TreeDecoratorTest extends LimbTestCase
{
  var $tree;
  var $driver;
  var $toolkit;

  function TreeDecoratorTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->toolkit = new MockLimbToolkit($this);

    $this->tree = new MockTree($this);
    $this->decorator = new TreeDecorator($this->tree);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->tree->tally();

    Limb :: restoreToolkit();
  }

  function testIsNode()
  {
    $id = 100;
    $this->tree->expectOnce('isNode', array($id));
    $this->tree->setReturnValue('isNode', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->isNode($id));
  }

  function testGetNode()
  {
    $id = 100;
    $this->tree->expectOnce('getNode', array($id));
    $this->tree->setReturnValue('getNode', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->getNode($id));
  }

  function testGetParent()
  {
    $id = 100;
    $this->tree->expectOnce('getParent', array($id));
    $this->tree->setReturnValue('getParent', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->getParent($id));
  }

  function testGetParents()
  {
    $id = 100;
    $this->tree->expectOnce('getParents', array($id));
    $this->tree->setReturnValue('getParents', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->getParents($id));
  }

  function testGetSiblings()
  {
    $id = 100;
    $this->tree->expectOnce('getSiblings', array($id));
    $this->tree->setReturnValue('getSiblings', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->getSiblings($id));
  }

  function testGetChildren()
  {
    $id = 100;
    $this->tree->expectOnce('getChildren', array($id));
    $this->tree->setReturnValue('getChildren', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->getChildren($id));
  }

  function testCountChildren()
  {
    $id = 100;
    $this->tree->expectOnce('countChildren', array($id));
    $this->tree->setReturnValue('countChildren', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->countChildren($id));
  }

  function testCreateRootNode()
  {
    $values = array('identifier' => 'test');
    $this->tree->expectOnce('createRootNode', array($values));
    $this->tree->setReturnValue('createRootNode', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->createRootNode($values));
  }

  function testCreateSubNode()
  {
    $values = array('identifier' => 'test');
    $id = 100;
    $this->tree->expectOnce('createSubNode', array($id, $values));
    $this->tree->setReturnValue('createSubNode', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->createSubNode($id, $values));
  }

  function testDeleteNode()
  {
    $id = 100;
    $this->tree->expectOnce('deleteNode', array($id));
    $this->tree->setReturnValue('deleteNode', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->deleteNode($id));
  }

  function testUpdateNode()
  {
    $id = 100;
    $values = array('identifier' => 'test');
    $internal = true;

    $this->tree->expectOnce('updateNode', array($id, $values, $internal));
    $this->tree->setReturnValue('updateNode', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->updateNode($id, $values, $internal));
  }

  function testMoveTree()
  {
    $id = 100;
    $target_id = 101;

    $this->tree->expectOnce('moveTree', array($id, $target_id));
    $this->tree->setReturnValue('moveTree', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->moveTree($id, $target_id));
  }

  function testSetDumbMode()
  {
    $status = true;
    $this->tree->expectOnce('setDumbMode', array($status));
    $this->decorator->setDumbMode($status);
  }

  function testGetAllNodes()
  {
    $this->tree->expectOnce('getAllNodes');
    $this->tree->setReturnValue('getAllNodes', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->getAllNodes());
  }

  function testGetNodesByIds()
  {
    $ids_arr = array(100);
    $this->tree->expectOnce('getNodesByIds', array($ids_arr));
    $this->tree->setReturnValue('getNodesByIds', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->getNodesByIds($ids_arr));
  }

  function testGetMaxChildIdentifier()
  {
    $id = 100;
    $this->tree->expectOnce('getMaxChildIdentifier', array($id));
    $this->tree->setReturnValue('getMaxChildIdentifier', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->getMaxChildIdentifier($id));
  }

  function testGetNodeByPath()
  {
    $path = 'test/path';
    $this->tree->expectOnce('getNodeByPath', array($path, '/'));
    $this->tree->setReturnValue('getNodeByPath', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->getNodeByPath($path));
  }

  function testGetSubBranch()
  {
    $id = 100;
    $this->tree->expectOnce('getSubBranch', array($id, -1, false, false));
    $this->tree->setReturnValue('getSubBranch', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->getSubBranch($id));
  }

  function testGetSubBranchByPath()
  {
    $path = '/test/path';
    $this->tree->expectOnce('getSubBranchByPath', array($path, -1, false, false));
    $this->tree->setReturnValue('getSubBranchByPath', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->getSubBranchByPath($path));
  }

  function testGetRootNodes()
  {
    $this->tree->expectOnce('getRootNodes');
    $this->tree->setReturnValue('getRootNodes', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->getRootNodes());
  }

  function testIsNodeExpanded()
  {
    $id = 100;
    $this->tree->expectOnce('isNodeExpanded', array($id));
    $this->tree->setReturnValue('isNodeExpanded', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->isNodeExpanded($id));
  }

  function testToggleNode()
  {
    $id = 100;
    $this->tree->expectOnce('toggleNode', array($id));
    $this->tree->setReturnValue('toggleNode', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->toggleNode($id));
  }

  function testExpandNode()
  {
    $id = 100;
    $this->tree->expectOnce('expandNode', array($id));
    $this->tree->setReturnValue('expandNode', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->expandNode($id));
  }

  function testCollapseNode()
  {
    $id = 100;
    $this->tree->expectOnce('collapseNode', array($id));
    $this->tree->setReturnValue('collapseNode', $res = 'whatever');
    $this->assertEqual($res, $this->decorator->collapseNode($id));
  }

  function testCanAddNodeTrue()
  {
    $id = 100;
    $this->tree->expectOnce('isNode', array($id));
    $this->tree->setReturnValue('isNode', true);

    $this->assertTrue($this->decorator->canAddNode($id));
  }

  function testCanAddNodeFalse()
  {
    $id = 100;
    $this->tree->expectOnce('isNode', array($id));
    $this->tree->setReturnValue('isNode', false);

    $this->assertFalse($this->decorator->canAddNode($id));
  }

  function testCanDeleteNodeTrue()
  {
    $id = 100;
    $this->tree->expectOnce('countChildren', array($id));
    $this->tree->setReturnValue('countChildren', 0);

    $this->assertTrue($this->decorator->canDeleteNode($id));
  }

  function testCanDeleteNodeTrue2()
  {
    $id = 100;
    $this->tree->expectOnce('countChildren', array($id));
    $this->tree->setReturnValue('countChildren', false);

    $this->assertTrue($this->decorator->canDeleteNode($id));
  }

  function testGetPathToNode()
  {
    $node = array('id' => 100, 'identifier' => '3');

    $this->tree->expectOnce('getPathToNode', array($node, '/'));
    $this->tree->setReturnValue('getPathToNode', $path = '/1/2/3');
    $this->assertEqual($path, $this->decorator->getPathToNode($node));
  }
}

?>
