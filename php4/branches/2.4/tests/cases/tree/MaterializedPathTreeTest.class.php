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
require_once(LIMB_DIR . '/core/tree/MaterializedPathTree.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');
require_once(LIMB_DIR . '/core/util/ComplexArray.class.php');

define('MATERIALIZED_PATH_TEST_TABLE', 'test_materialized_path_tree');

class MaterializedPathTreeTestVersion extends MaterializedPathTree
{
  var $_node_table = MATERIALIZED_PATH_TEST_TABLE;
}

class MaterializedPathTreeTest extends LimbTestCase
{
  var $db = null;
  var $driver = null;

  function setUp()
  {
    $this->db =& LimbDbPool :: getConnection();

    $this->driver = new MaterializedPathTreeTestVersion();

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->sqlDelete(MATERIALIZED_PATH_TEST_TABLE);
    $this->db->sqlDelete('sys_site_object');
    $this->db->sqlDelete('sys_class');
  }

  function testGetNodeFailed()
  {
    $this->assertIdentical(false, $this->driver->getNode(10000));
  }

  function testGetNode()
  {
    $node = array(
      'identifier' => 'test',
      'object_id' => 100,
      'id' => 10,
      'path' => '/10/',
      'root_id' => 10,
      'level' => 2,
      'parent_id' => 1000,
      'children' => 0
    );

    $this->db->sqlInsert(MATERIALIZED_PATH_TEST_TABLE, $node);

    $this->assertEqual($node, $this->driver->getNode(10));
  }

  function testGetParentFailed()
  {
    $this->assertIdentical(false, $this->driver->getParent(1000));
  }

  function testGetParent()
  {
    $root_node = array(
      'identifier' => 'root',
      'object_id' => 100,
      'id' => 1,
      'path' => '/1/',
      'root_id' => 1,
      'level' => 1,
      'parent_id' => 0,
      'children' => 1
    );

    $this->db->sqlInsert(MATERIALIZED_PATH_TEST_TABLE, $root_node);

    $node = array(
      'identifier' => 'test',
      'object_id' => 100,
      'id' => 10,
      'path' => '/1/10/',
      'root_id' => 1,
      'level' => 2,
      'parent_id' => 1,
      'children' => 0
    );

    $this->db->sqlInsert(MATERIALIZED_PATH_TEST_TABLE, $node);

    $this->assertEqual($root_node, $this->driver->getParent(10));
    $this->assertIdentical(false, $this->driver->getParent(1));
  }

  function testCreateRootNode()
  {
    $node = array(
      'identifier' => 'test',
      'object_id' => 100,
      'id' => 0,
      'path' => '/0/',
      'root_id' => 0,
      'level' => 23,
      'parent_id' => 1000,
      'children' => 1000
    );

    $node_id = $this->driver->createRootNode($node);

    $this->assertNotIdentical($node_id, false);

    $this->db->sqlSelect(MATERIALIZED_PATH_TEST_TABLE);
    $arr = $this->db->getArray();
    $this->assertEqual(sizeof($arr), 1);

    $row = current($arr);

    $this->assertEqual($row['id'], $node_id, 'invalid parameter: id');
    $this->assertEqual($row['object_id'], 100, 'invalid parameter: object_id');
    $this->assertEqual($row['identifier'], 'test', 'invalid parameter: identifier');
    $this->assertEqual($row['level'], 1, 'invalid parameter: level');
    $this->assertEqual($row['parent_id'], 0, 'invalid parameter: parent_id');
    $this->assertEqual($row['root_id'], $node_id, 'invalid parameter: root_id');
    $this->assertEqual($row['path'], '/' . $node_id . '/', 'invalid parameter: path');
    $this->assertEqual($row['children'], 0, 'invalid parameter: children');
  }

  function testCreateRootNodeDumb()
  {
    $node = array(
      'identifier' => 'test',
      'object_id' => 100,
      'id' => 1000000000,
      'path' => '/0/',
      'root_id' => 0,
      'level' => 23,
      'parent_id' => 1000,
      'children' => 10000
    );

    $this->driver->setDumbMode();

    $node_id = $this->driver->createRootNode($node);

    $this->assertEqual($node_id, 1000000000);

    $this->db->sqlSelect(MATERIALIZED_PATH_TEST_TABLE);
    $arr = $this->db->getArray();
    $row = current($arr);

    $this->assertEqual($row['id'], 1000000000, 'invalid parameter: id');
  }

  function testCreateSubNodeFailed()
  {
    $this->driver->createSubNode(100000, array());
  }

  function testCreateSubNode()
  {
    $parent_node_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));

    $parent_node = $this->driver->getNode($parent_node_id);

    $sub_node = array(
      'identifier' => 'test',
      'object_id' => 100,
      'id' => 0,
      'path' => '/0/',
      'root_id' => 0,
      'level' => 23,
      'parent_id' => 1000,
      'children' => 1000
    );

    $sub_node_id = $this->driver->createSubNode($parent_node_id, $sub_node);

    $this->assertNotIdentical($sub_node_id, false);

    $this->db->sqlSelect(MATERIALIZED_PATH_TEST_TABLE);
    $arr = $this->db->getArray();
    $this->assertEqual(sizeof($arr), 2);

    $row = reset($arr);

    $this->assertEqual($row['children'], 1, 'invalid parameter in parent: children');

    $row = end($arr);

    $this->assertEqual($row['id'], $sub_node_id, 'invalid parameter: id');
    $this->assertEqual($row['object_id'], 100, 'invalid parameter: object_id');
    $this->assertEqual($row['identifier'], 'test', 'invalid parameter: identifier');
    $this->assertEqual($row['level'], 2, 'invalid parameter: level');
    $this->assertEqual($row['parent_id'], $parent_node_id, 'invalid parameter: parent_id');
    $this->assertEqual($row['root_id'], $parent_node['root_id'], 'invalid parameter: root_id');
    $this->assertEqual($row['path'], '/' . $parent_node_id . '/'. $sub_node_id . '/', 'invalid parameter: path');
    $this->assertEqual($row['children'], 0, 'invalid parameter: children');
  }

  function testCreateSubNodeDumb()
  {
    $parent_node_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));

    $parent_node = $this->driver->getNode($parent_node_id);
    $this->driver->setDumbMode();

    $sub_node = array(
      'identifier' => 'test',
      'object_id' => 100,
      'id' => 12,
      'path' => '/0/',
      'root_id' => 0,
      'level' => 23,
      'parent_id' => 1000,
      'children' => 1000
    );

    $sub_node_id = $this->driver->createSubNode($parent_node_id, $sub_node);

    $this->assertNotIdentical($sub_node_id, false);
    $this->assertEqual($sub_node_id, 12);

    $this->db->sqlSelect(MATERIALIZED_PATH_TEST_TABLE);
    $arr = $this->db->getArray();
    $row = end($arr);

    $this->assertEqual($row['id'], $sub_node_id, 'invalid parameter: id');
  }

  function testGetMaxIdentifierFailed()
  {
    $this->assertIdentical(false, $this->driver->getMaxChildIdentifier(1000));
  }

  function testGetMaxIdentifier()
  {
    $root_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));

    $this->assertEqual(0, $this->driver->getMaxChildIdentifier($root_id));

    $sub_node_id_1_1 = $this->driver->createSubNode($root_id, array('identifier' => 'test1', 'object_id' => 20));
    $sub_node_id_1_2 = $this->driver->createSubNode($root_id, array('identifier' => 'test3', 'object_id' => 10));
    $sub_node_id_1_3 = $this->driver->createSubNode($root_id, array('identifier' => 'test2', 'object_id' => 10));

    $this->assertEqual('test3', $this->driver->getMaxChildIdentifier($root_id));
  }

  function testGetMaxIdentifierNaturalSort()
  {
    $root_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));

    $this->assertEqual(0, $this->driver->getMaxChildIdentifier($root_id));

    $sub_node_id_1_1 = $this->driver->createSubNode($root_id, array('identifier' => 'test8', 'object_id' => 20));
    $sub_node_id_1_2 = $this->driver->createSubNode($root_id, array('identifier' => 'test9', 'object_id' => 10));
    $sub_node_id_1_3 = $this->driver->createSubNode($root_id, array('identifier' => 'test10', 'object_id' => 10));

    $this->assertEqual('test10', $this->driver->getMaxChildIdentifier($root_id));
  }

  function testDeleteNodeFailed()
  {
    $this->driver->deleteNode(100000);
  }

  function testDeleteNode()
  {
    $parent_node_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));
    $sub_node_id1 = $this->driver->createSubNode($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
    $sub_node_id2 = $this->driver->createSubNode($parent_node_id, array('identifier' => 'test2', 'object_id' => 20));
    $this->driver->createSubNode($sub_node_id1, array('identifier' => 'test0', 'object_id' => 20));

    $this->driver->deleteNode($sub_node_id1);

    $this->db->sqlSelect(MATERIALIZED_PATH_TEST_TABLE);
    $arr = $this->db->getArray();
    $this->assertEqual(sizeof($arr), 2);

    $row = reset($arr);

    $this->assertEqual($row['children'], 1, 'invalid parent parameter: children');

    $row = end($arr);

    $this->assertEqual($row['id'], $sub_node_id2, 'invalid parameter: id');
    $this->assertEqual($row['object_id'], 20, 'invalid parameter: object_id');
    $this->assertEqual($row['identifier'], 'test2', 'invalid parameter: identifier');
    $this->assertEqual($row['level'], 2, 'invalid parameter: level');
    $this->assertEqual($row['parent_id'], $parent_node_id, 'invalid parameter: parent_id');
    $this->assertEqual($row['children'], 0, 'invalid parameter: children');
  }

  function testIsNode()
  {
    $parent_node_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));
    $sub_node_id = $this->driver->createSubNode($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));

    $this->assertTrue($this->driver->isNode($sub_node_id));
    $this->assertTrue($this->driver->isNode($parent_node_id));
    $this->assertFalse($this->driver->isNode(1000));
  }

  function testGetParentsFailed()
  {
    $this->assertFalse($this->driver->getParents(10000));
  }

  function testGetParents()
  {
    $parent_node_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));
    $this->driver->createSubNode($parent_node_id, array('identifier' => 'test0', 'object_id' => 20));

    $sub_node_id1 = $this->driver->createSubNode($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
    $sub_node_id2 = $this->driver->createSubNode($sub_node_id1, array('identifier' => 'test2', 'object_id' => 20));

    $nodes = $this->driver->getParents($sub_node_id2);

    $this->assertEqual(sizeof($nodes), 2);
    $this->_checkProperNesting($nodes, __LINE__);
    $this->_checkResultNodesArray($nodes, __LINE__);

    $row = reset($nodes);

    $this->assertEqual($row['id'], $parent_node_id, 'invalid parameter: id');
    $this->assertEqual($row['identifier'], 'root', 'invalid parameter: identifier');
    $this->assertEqual($row['object_id'], 10, 'invalid parameter: object_id');

    $row = end($nodes);

    $this->assertEqual($row['id'], $sub_node_id1, 'invalid parameter: id');
    $this->assertEqual($row['identifier'], 'test1', 'invalid parameter: identifier');
    $this->assertEqual($row['object_id'], 20, 'invalid parameter: object_id');

    $nodes = $this->driver->getParents($sub_node_id1);

    $this->assertEqual(sizeof($nodes), 1);
    $this->_checkProperNesting($nodes, __LINE__);
    $this->_checkResultNodesArray($nodes, __LINE__);
  }

  function testGetChildrenFailed()
  {
    $this->assertFalse($this->driver->getChildren(10000));
  }

  function testGetChildren()
  {
    $parent_node_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));
    $sub_node_id1 = $this->driver->createSubNode($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
    $sub_node_id2 = $this->driver->createSubNode($parent_node_id, array('identifier' => 'test2', 'object_id' => 20));
    $this->driver->createSubNode($sub_node_id1, array('identifier' => 'test0', 'object_id' => 20));

    $nodes = $this->driver->getChildren($parent_node_id);

    $this->assertEqual(sizeof($nodes), 2);
    $this->_checkResultNodesArray($nodes, __LINE__);

    $row = reset($nodes);

    $this->assertEqual($row['id'], $sub_node_id1, 'invalid parameter: id');
    $this->assertEqual($row['identifier'], 'test1', 'invalid parameter: identifier');
    $this->assertEqual($row['object_id'], 20, 'invalid parameter: object_id');

    $row = end($nodes);

    $this->assertEqual($row['id'], $sub_node_id2, 'invalid parameter: id');
    $this->assertEqual($row['identifier'], 'test2', 'invalid parameter: identifier');
    $this->assertEqual($row['object_id'], 20, 'invalid parameter: object_id');
  }

  function testCountChildrenFailed()
  {
    $this->assertFalse($this->driver->countChildren(10000));
  }

  function testCountChildren()
  {
    $parent_node_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));
    $sub_node_id1 = $this->driver->createSubNode($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
    $sub_node_id2 = $this->driver->createSubNode($parent_node_id, array('identifier' => 'test2', 'object_id' => 20));
    $this->driver->createSubNode($sub_node_id1, array('identifier' => 'test0', 'object_id' => 20));

    $this->assertEqual(2, $this->driver->countChildren($parent_node_id));
  }

  function testGetSiblingsFailed()
  {
    $this->assertFalse($this->driver->getSiblings(10000));
  }

  function testGetSiblings()
  {
    $parent_node_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));
    $sub_node_id1 = $this->driver->createSubNode($parent_node_id, array('identifier' => 'test1', 'object_id' => 20));
    $sub_node_id2 = $this->driver->createSubNode($parent_node_id, array('identifier' => 'test2', 'object_id' => 20));
    $this->driver->createSubNode($sub_node_id1, array('identifier' => 'test0', 'object_id' => 20));

    $nodes = $this->driver->getSiblings($sub_node_id2);

    $this->assertEqual(sizeof($nodes), 2);

    $row = reset($nodes);

    $this->assertEqual($row['id'], $sub_node_id1, 'invalid parameter: id');
    $this->assertEqual($row['identifier'], 'test1', 'invalid parameter: identifier');
    $this->assertEqual($row['object_id'], 20, 'invalid parameter: object_id');

    $row = end($nodes);

    $this->assertEqual($row['id'], $sub_node_id2, 'invalid parameter: id');
    $this->assertEqual($row['identifier'], 'test2', 'invalid parameter: identifier');
    $this->assertEqual($row['object_id'], 20, 'invalid parameter: object_id');
  }

  function testUpdateNodeFailed()
  {
    $this->assertFalse($this->driver->updateNode(10000, array()));
  }

  function testUpdateNode()
  {
    $node_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));

    $node = array(
      'identifier' => 'test',
      'object_id' => 100,
      'id' => 12,
      'path' => '/0/',
      'root_id' => 0,
      'level' => 23,
      'parent_id' => 1000,
      'children' => 1000
    );

    $this->assertTrue($this->driver->updateNode($node_id, $node));

    $updated_node = $this->driver->getNode($node_id);

    $this->assertEqual($updated_node['object_id'], 100, 'invalid parameter: object_id');
    $this->assertEqual($updated_node['identifier'], 'test', 'invalid parameter: identifier');
    $this->assertNotEqual($updated_node, $node, 'invalid update');
  }

  function testMoveTreeFailed()
  {
    $this->assertFalse($this->driver->moveTree(1, 1));

    $this->assertFalse($this->driver->moveTree(1, 2));

    $node_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));
    $sub_node_id = $this->driver->createSubNode($node_id, array('identifier' => 'test', 'object_id' => 10));

    $this->assertFalse($this->driver->moveTree($node_id, $node_id-1));

    $this->assertFalse($this->driver->moveTree($node_id, $sub_node_id));
  }

  function testMoveTree()
  {
    $root_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));
    $sub_node_id_1 = $this->driver->createSubNode($root_id, array('identifier' => 'test', 'object_id' => 10));
    $sub_node_id_1_1 = $this->driver->createSubNode($sub_node_id_1, array('identifier' => 'test', 'object_id' => 10));
    $sub_node_id_1_1_1 = $this->driver->createSubNode($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 10));
    $sub_node_id_1_1_2 = $this->driver->createSubNode($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 10));

    $root_id_2 = $this->driver->createRootNode( array('identifier' => 'test', 'object_id' => 10));
    $sub_node_id_2 = $this->driver->createSubNode($root_id_2, array('identifier' => 'test', 'object_id' => 10));

    $root_node = $this->driver->getNode($root_id);
    $this->assertEqual($root_node['children'], 1, 'invalid parent parameter: children');

    $this->assertTrue($this->driver->moveTree($sub_node_id_1, $sub_node_id_2));

    $root_node = $this->driver->getNode($root_id);
    $this->assertEqual($root_node['children'], 0, 'invalid parent parameter: children');

    $sub_node_2 = $this->driver->getNode($sub_node_id_2);
    $this->assertEqual($sub_node_2['children'], 1, 'invalid parent parameter: children');

    $current_path = '/' . $root_id_2 . '/' . $sub_node_id_2 . '/';

    $sub_node_1 = $this->driver->getNode($sub_node_id_1);

    $current_path .= $sub_node_id_1 . '/';
    $this->assertEqual($sub_node_1['level'], 3, 'invalid parameter: level');
    $this->assertEqual($sub_node_1['parent_id'], $sub_node_id_2, 'invalid parameter: parent_id');
    $this->assertEqual($sub_node_1['path'], $current_path, 'invalid parameter: path');
    $this->assertEqual($sub_node_1['root_id'], $root_id_2, 'invalid parameter: root_id');

    $current_path .= $sub_node_id_1_1 . '/';
    $sub_node_1_1 = $this->driver->getNode($sub_node_id_1_1);

    $this->assertEqual($sub_node_1_1['level'], 4, 'invalid parameter: level');
    $this->assertEqual($sub_node_1_1['parent_id'], $sub_node_id_1, 'invalid parameter: parent_id');
    $this->assertEqual($sub_node_1_1['path'], $current_path , 'invalid parameter: path');
    $this->assertEqual($sub_node_1_1['root_id'], $root_id_2, 'invalid parameter: root_id');

    $sub_node_1_1_1 = $this->driver->getNode($sub_node_id_1_1_1);

    $this->assertEqual($sub_node_1_1_1['level'], 5, 'invalid parameter: level');
    $this->assertEqual($sub_node_1_1_1['parent_id'], $sub_node_id_1_1, 'invalid parameter: parent_id');
    $this->assertEqual($sub_node_1_1_1['path'], $current_path . $sub_node_id_1_1_1 . '/', 'invalid parameter: path');
    $this->assertEqual($sub_node_1_1_1['root_id'], $root_id_2, 'invalid parameter: root_id');

    $sub_node_1_1_2 = $this->driver->getNode($sub_node_id_1_1_2);

    $this->assertEqual($sub_node_1_1_2['level'], 5, 'invalid parameter: level');
    $this->assertEqual($sub_node_1_1_2['parent_id'], $sub_node_id_1_1, 'invalid parameter: parent_id');
    $this->assertEqual($sub_node_1_1_2['path'], $current_path . $sub_node_id_1_1_2 . '/', 'invalid parameter: path');
    $this->assertEqual($sub_node_1_1_2['root_id'], $root_id_2, 'invalid parameter: root_id');
  }

  function testGetSubBranchFailed()
  {
    $this->assertFalse($this->driver->getSubBranch(1));
  }

  function testGetSubBranch()
  {
    $root_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));
    $sub_node_id_1 = $this->driver->createSubNode($root_id, array('identifier' => 'test', 'object_id' => 10));
    $sub_node_id_1_1 = $this->driver->createSubNode($sub_node_id_1, array('identifier' => 'test', 'object_id' => 20));
    $sub_node_id_1_1_1 = $this->driver->createSubNode($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 10));
    $sub_node_id_1_1_2 = $this->driver->createSubNode($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 10));

    //getting all
    $branch = $this->driver->getSubBranch($sub_node_id_1);
    $this->assertEqual(3, sizeof($branch));
    $this->_checkResultNodesArray($branch, __LINE__);
    $this->_checkProperNesting($branch, __LINE__);

    $node = current($branch);
    $this->assertEqual($node['id'], $sub_node_id_1_1, 'invalid parameter: id');

    //getting at unlimited depth, including node
    $branch = $this->driver->getSubBranch($sub_node_id_1, -1, true);
    $this->assertEqual(4, sizeof($branch));
    $this->_checkResultNodesArray($branch, __LINE__);
    $this->_checkProperNesting($branch, __LINE__);

    //getting at depth = 1
    $branch = $this->driver->getSubBranch($sub_node_id_1, 1);
    $this->assertEqual(1, sizeof($branch));
    $this->_checkResultNodesArray($branch,  __LINE__);
    $this->_checkProperNesting($branch, __LINE__);

    //getting at depth = 1, including node
    $branch = $this->driver->getSubBranch($sub_node_id_1, 1, true);
    $this->assertEqual(2, sizeof($branch));
    $this->_checkResultNodesArray($branch,  __LINE__);
    $this->_checkProperNesting($branch, __LINE__);
  }

  function testGetSubBranchCheckExpandedParents()
  {
    $this->db->sqlInsert('sys_site_object', array('id' => 10));
    $this->db->sqlInsert('sys_site_object', array('id' => 20));

    //creating subtree
    $root_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));
    $sub_node_id_1 = $this->driver->createSubNode($root_id, array('identifier' => 'test', 'object_id' => 10));
    $sub_node_id_1_1 = $this->driver->createSubNode($sub_node_id_1, array('identifier' => 'test', 'object_id' => 10));
    $sub_node_id_1_1_1 = $this->driver->createSubNode($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 20));
    $sub_node_id_1_1_2 = $this->driver->createSubNode($sub_node_id_1_1, array('identifier' => 'test', 'object_id' => 20));

    //creating second subtree to check that only one subtree uses during fetch subbranch
    $root_id_2 = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));
    $sub_node_id_2 = $this->driver->createSubNode($root_id_2, array('identifier' => 'test', 'object_id' => 10));
    $sub_node_id_2_1 = $this->driver->createSubNode($sub_node_id_2, array('identifier' => 'test', 'object_id' => 10));
    $sub_node_id_2_1_1 = $this->driver->createSubNode($sub_node_id_2_1, array('identifier' => 'test', 'object_id' => 20));
    $sub_node_id_2_1_2 = $this->driver->createSubNode($sub_node_id_2_1, array('identifier' => 'test', 'object_id' => 20));

    $this->driver->checkExpandedParents();

    $this->driver->expandNode($sub_node_id_1);

    $this->driver->expandNode($sub_node_id_2);
    $this->driver->expandNode($sub_node_id_2_1);

    //getting at unlimited depth, including node, checking expanded parents
    $branch = $this->driver->getSubBranch($root_id, -1, true, true);
    $this->assertEqual(3, sizeof($branch));
    $this->_checkResultNodesArray($branch, __LINE__);
    $this->_checkProperNesting($branch, __LINE__);

    $this->driver->collapseNode($root_id);
    $this->driver->expandNode($sub_node_id_1_1);

    //getting at unlimited depth, including node, checking expanded parents
    $branch = $this->driver->getSubBranch($root_id, -1, true, true);
    $this->assertEqual(1, sizeof($branch));
    $this->_checkResultNodesArray($branch, __LINE__);
    $this->_checkProperNesting($branch, __LINE__);
  }

  function testGetNodeByPathFailed()
  {
    $root_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));
    $sub_node_id_1 = $this->driver->createSubNode($root_id, array('identifier' => '1_test', 'object_id' => 10));
    $sub_node_id_1_1 = $this->driver->createSubNode($sub_node_id_1, array('identifier' => '1_1_test', 'object_id' => 20));
    $sub_node_id_1_1_1 = $this->driver->createSubNode($sub_node_id_1_1, array('identifier' => '1_1_1_test', 'object_id' => 10));
    $sub_node_id_1_1_2 = $this->driver->createSubNode($sub_node_id_1_1, array('identifier' => '1_1_2_test', 'object_id' => 10));

    $root_id2 = $this->driver->createRootNode(array('identifier' => 'root2', 'object_id' => 10));
    $sub_node_id_2 = $this->driver->createSubNode($root_id2, array('identifier' => '2_test', 'object_id' => 10));

    $this->assertFalse($this->driver->getNodeByPath(''));
    $this->assertFalse($this->driver->getNodeByPath('/root///'));
    $this->assertFalse($this->driver->getNodeByPath('/root/wow/yo'));
    $this->assertFalse($this->driver->getNodeByPath('/root/2_test'));
  }

  function testGetNodeByPath()
  {
    $root_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));
    $sub_node_id_1 = $this->driver->createSubNode($root_id, array('identifier' => 'test1', 'object_id' => 10));
    $sub_node_id_1_1 = $this->driver->createSubNode($sub_node_id_1, array('identifier' => 'test1', 'object_id' => 20));
    $sub_node_id_1_1_1 = $this->driver->createSubNode($sub_node_id_1_1, array('identifier' => 'test1', 'object_id' => 10));
    $sub_node_id_1_1_2 = $this->driver->createSubNode($sub_node_id_1_1, array('identifier' => 'test2', 'object_id' => 10));

    $node = $this->driver->getNodeByPath('/root/');
    $this->assertEqual($node['id'], $root_id);
    $this->_checkResultNodesArray($node, __LINE__);

    $node = $this->driver->getNodeByPath('/root/test1/test1/');
    $this->assertEqual($node['id'], $sub_node_id_1_1);
    $this->_checkResultNodesArray($node,  __LINE__);

    $node = $this->driver->getNodeByPath('/root/test1/test1/test2');
    $this->assertEqual($node['id'], $sub_node_id_1_1_2);
    $this->_checkResultNodesArray($node,  __LINE__);

    $node = $this->driver->getNodeByPath('/root/test1/test1/test1/');
    $this->assertEqual($node['id'], $sub_node_id_1_1_1);
    $this->_checkResultNodesArray($node,  __LINE__);
  }

  function testGetSubBranchByPathFailed()
  {
    $this->assertFalse($this->driver->getSubBranch(1));
  }

  function testGetNodesByIds()
  {
    $root_id = $this->driver->createRootNode(array('identifier' => 'root', 'object_id' => 10));
    $sub_node_id_1 = $this->driver->createSubNode($root_id, array('identifier' => 'test1', 'object_id' => 10));
    $sub_node_id_1_1 = $this->driver->createSubNode($sub_node_id_1, array('identifier' => 'test1', 'object_id' => 20));
    $sub_node_id_1_1_1 = $this->driver->createSubNode($sub_node_id_1_1, array('identifier' => 'test1', 'object_id' => 10));
    $sub_node_id_1_1_2 = $this->driver->createSubNode($sub_node_id_1_1, array('identifier' => 'test2', 'object_id' => 10));

    $nodes = $this->driver->getNodesByIds(
      array(
        $root_id,
        $sub_node_id_1,
        $sub_node_id_1_1,
        $sub_node_id_1_1_2,
        $sub_node_id_1_1_1,
        -1
      )
    );

    $this->assertEqual(sizeof($nodes), 5);
    $this->_checkResultNodesArray($nodes,  __LINE__);

    $nodes = $this->driver->getNodesByIds(
      array(
        $sub_node_id_1,
        $sub_node_id_1_1,
        $sub_node_id_1_1_1,
        -1
      )
    );

    $this->assertEqual(sizeof($nodes), 3);
    $this->_checkResultNodesArray($nodes,  __LINE__);

    $nodes = $this->driver->getNodesByIds(
      array()
    );

    $this->assertEqual(sizeof($nodes), 0);
  }

  function _checkResultNodesArray($nodes, $line='')
  {
    if(isset($nodes['object_id']))
      $this->assertEqual($this->driver->getNode($nodes['id']), $nodes, 'at line: ' . $line);
    else
      foreach($nodes as $id => $node)
        $this->assertEqual($this->driver->getNode($id), $node, 'at line: ' . $line);
  }

  function _checkProperNesting($nodes, $line='')
  {
    $paths[] = ComplexArray :: getMinColumnValue('path', $nodes, $index);

    $counter = 0;
    foreach($nodes as $id => $node)
    {
      if($counter == 0)
      {
        $this->assertEqual($node['path'], $paths[0],
          'first element path is invalid: ' . $node['path'] . ' , expected : ' . $paths[0] . ' at line: ' . $line);
      }
      elseif(preg_match('~^(.*/)[^/]+/$~', $node['path'], $matches))
      {
        $prev_path = $matches[1];
        $this->assertTrue(in_array($prev_path, $paths),
          'path is improperly nested: ' . $node['path'] . ' , expected parent not found: ' . $prev_path . ' at line: ' . $line);
      }
      else
      {
        $this->assertFalse(true, 'path is invalid: ' . $node['path'] . ' at line: ' . $line);
      }

      $paths[] = $node['path'];
      $counter++;
    }
  }

}
?>
