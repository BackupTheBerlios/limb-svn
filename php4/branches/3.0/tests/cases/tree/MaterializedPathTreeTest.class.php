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
require_once(LIMB_DIR . '/core/db/SimpleDb.class.php');
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

  function MaterializedPathTreeTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function _createTreeImp()
  {
    return new MaterializedPathTreeTestVersion();
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $this->db = new SimpleDb($toolkit->getDbConnection());

    $this->imp =& $this->_createTreeImp();

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete(MATERIALIZED_PATH_TEST_TABLE);
    $this->db->delete('sys_class');
  }

  function testGetNodeFailed()
  {
    $this->assertNull($this->imp->getNode(10000));
  }

  function testGetNode()
  {
    $node = array(
      'identifier' => 'test',
      'id' => 10,
      'path' => '/10/',
      'root_id' => 10,
      'level' => 2,
      'parent_id' => 1000,
      'children' => 0
    );

    $this->db->insert(MATERIALIZED_PATH_TEST_TABLE, $node);

    $this->assertEqual($node, $this->imp->getNode(10));
  }

  function testGetParentFailed()
  {
    $this->assertNull($this->imp->getParent(1000));
  }

  function testGetParent()
  {
    $root_node = array(
      'identifier' => 'root',
      'id' => 1,
      'path' => '/1/',
      'root_id' => 1,
      'level' => 1,
      'parent_id' => 0,
      'children' => 1
    );

    $this->db->insert(MATERIALIZED_PATH_TEST_TABLE, $root_node);

    $node = array(
      'identifier' => 'test',
      'id' => 10,
      'path' => '/1/10/',
      'root_id' => 1,
      'level' => 2,
      'parent_id' => 1,
      'children' => 0
    );

    $this->db->insert(MATERIALIZED_PATH_TEST_TABLE, $node);

    $this->assertEqual($root_node, $this->imp->getParent(10));
    $this->assertNull($this->imp->getParent(1));
  }

  function testCreateRootNode()
  {
    $node = array(
      'identifier' => 'test',
      'id' => 0,
      'path' => '/0/',
      'root_id' => 0,
      'level' => 23,
      'parent_id' => 1000,
      'children' => 1000
    );

    $node_id = $this->imp->createRootNode($node);

    $this->assertNotIdentical($node_id, false);

    $rs = $this->db->select(MATERIALIZED_PATH_TEST_TABLE);
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);

    $row = current($arr);

    $this->assertEqual($row['id'], $node_id, 'invalid parameter: id');
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
      'id' => 1000000000,
      'path' => '/0/',
      'root_id' => 0,
      'level' => 23,
      'parent_id' => 1000,
      'children' => 10000
    );

    $this->imp->setDumbMode();

    $node_id = $this->imp->createRootNode($node);

    $this->assertEqual($node_id, 1000000000);

    $rs =& $this->db->select(MATERIALIZED_PATH_TEST_TABLE);
    $arr = $rs->getArray();
    $row = current($arr);

    $this->assertEqual($row['id'], 1000000000, 'invalid parameter: id');
  }

  function testCreateSubNodeFailed()
  {
    $this->imp->createSubNode(100000, array());
  }

  function testCreateSubNode()
  {
    $parent_node_id = $this->imp->createRootNode(array('identifier' => 'root'));

    $parent_node = $this->imp->getNode($parent_node_id);

    $sub_node = array(
      'identifier' => 'test',
      'id' => 0,
      'path' => '/0/',
      'root_id' => 0,
      'level' => 23,
      'parent_id' => 1000,
      'children' => 1000
    );

    $sub_node_id = $this->imp->createSubNode($parent_node_id, $sub_node);

    $this->assertNotIdentical($sub_node_id, false);

    $rs =& $this->db->select(MATERIALIZED_PATH_TEST_TABLE);
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 2);

    $row = reset($arr);

    $this->assertEqual($row['children'], 1, 'invalid parameter in parent: children');

    $row = end($arr);

    $this->assertEqual($row['id'], $sub_node_id, 'invalid parameter: id');
    $this->assertEqual($row['identifier'], 'test', 'invalid parameter: identifier');
    $this->assertEqual($row['level'], 2, 'invalid parameter: level');
    $this->assertEqual($row['parent_id'], $parent_node_id, 'invalid parameter: parent_id');
    $this->assertEqual($row['root_id'], $parent_node['root_id'], 'invalid parameter: root_id');
    $this->assertEqual($row['path'], '/' . $parent_node_id . '/'. $sub_node_id . '/', 'invalid parameter: path');
    $this->assertEqual($row['children'], 0, 'invalid parameter: children');
  }

  function testCreateSubNodeDumb()
  {
    $parent_node_id = $this->imp->createRootNode(array('identifier' => 'root'));

    $parent_node = $this->imp->getNode($parent_node_id);
    $this->imp->setDumbMode();

    $sub_node = array(
      'identifier' => 'test',
      'id' => 12,
      'path' => '/0/',
      'root_id' => 0,
      'level' => 23,
      'parent_id' => 1000,
      'children' => 1000
    );

    $sub_node_id = $this->imp->createSubNode($parent_node_id, $sub_node);

    $this->assertNotIdentical($sub_node_id, false);
    $this->assertEqual($sub_node_id, 12);

    $rs =& $this->db->select(MATERIALIZED_PATH_TEST_TABLE);
    $arr = $rs->getArray();
    $row = end($arr);

    $this->assertEqual($row['id'], $sub_node_id, 'invalid parameter: id');
  }

  function testGetMaxIdentifierFailed()
  {
    $this->assertIdentical(false, $this->imp->getMaxChildIdentifier(1000));
  }

  function testGetMaxIdentifier()
  {
    $root_id = $this->imp->createRootNode(array('identifier' => 'root'));

    $this->assertEqual(0, $this->imp->getMaxChildIdentifier($root_id));

    $sub_node_id_1_1 = $this->imp->createSubNode($root_id, array('identifier' => 'test1'));
    $sub_node_id_1_2 = $this->imp->createSubNode($root_id, array('identifier' => 'test3'));
    $sub_node_id_1_3 = $this->imp->createSubNode($root_id, array('identifier' => 'test2'));

    $this->assertEqual('test3', $this->imp->getMaxChildIdentifier($root_id));
  }

  function testGetMaxIdentifierNaturalSort()
  {
    $root_id = $this->imp->createRootNode(array('identifier' => 'root'));

    $this->assertEqual(0, $this->imp->getMaxChildIdentifier($root_id));

    $sub_node_id_1_1 = $this->imp->createSubNode($root_id, array('identifier' => 'test8'));
    $sub_node_id_1_2 = $this->imp->createSubNode($root_id, array('identifier' => 'test9'));
    $sub_node_id_1_3 = $this->imp->createSubNode($root_id, array('identifier' => 'test10'));

    $this->assertEqual('test10', $this->imp->getMaxChildIdentifier($root_id));
  }

  function testDeleteNodeFailed()
  {
    $this->imp->deleteNode(100000);
  }

  function testDeleteNode()
  {
    $parent_node_id = $this->imp->createRootNode(array('identifier' => 'root'));
    $sub_node_id1 = $this->imp->createSubNode($parent_node_id, array('identifier' => 'test1'));
    $sub_node_id2 = $this->imp->createSubNode($parent_node_id, array('identifier' => 'test2'));
    $this->imp->createSubNode($sub_node_id1, array('identifier' => 'test0'));

    $this->imp->deleteNode($sub_node_id1);

    $rs =& $this->db->select(MATERIALIZED_PATH_TEST_TABLE);
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 2);

    $row = reset($arr);

    $this->assertEqual($row['children'], 1, 'invalid parent parameter: children');

    $row = end($arr);

    $this->assertEqual($row['id'], $sub_node_id2, 'invalid parameter: id');
    $this->assertEqual($row['identifier'], 'test2', 'invalid parameter: identifier');
    $this->assertEqual($row['level'], 2, 'invalid parameter: level');
    $this->assertEqual($row['parent_id'], $parent_node_id, 'invalid parameter: parent_id');
    $this->assertEqual($row['children'], 0, 'invalid parameter: children');
  }

  function testIsNode()
  {
    $parent_node_id = $this->imp->createRootNode(array('identifier' => 'root'));
    $sub_node_id = $this->imp->createSubNode($parent_node_id, array('identifier' => 'test1'));

    $this->assertTrue($this->imp->isNode($sub_node_id));
    $this->assertTrue($this->imp->isNode($parent_node_id));
    $this->assertFalse($this->imp->isNode(1000));
  }

  function testGetParentsFailed()
  {
    $this->assertFalse($this->imp->getParents(10000));
  }

  function testGetParents()
  {
    $parent_node_id = $this->imp->createRootNode(array('identifier' => 'root'));
    $this->imp->createSubNode($parent_node_id, array('identifier' => 'test0'));

    $sub_node_id1 = $this->imp->createSubNode($parent_node_id, array('identifier' => 'test1'));
    $sub_node_id2 = $this->imp->createSubNode($sub_node_id1, array('identifier' => 'test2'));

    $rs =& new SimpleDbDataset($this->imp->getParents($sub_node_id2));

    $this->assertEqual($rs->getTotalRowCount(), 2);
    $nodes = $rs->getArray('id');

    $this->_checkProperNesting($nodes);
    $this->_checkResultNodesArray($nodes);

    $row = reset($nodes);

    $this->assertEqual($row['id'], $parent_node_id, 'invalid parameter: id');
    $this->assertEqual($row['identifier'], 'root', 'invalid parameter: identifier');

    $row = end($nodes);

    $this->assertEqual($row['id'], $sub_node_id1, 'invalid parameter: id');
    $this->assertEqual($row['identifier'], 'test1', 'invalid parameter: identifier');

    $rs =& new SimpleDbDataset($this->imp->getParents($sub_node_id1));

    $this->assertEqual($rs->getTotalRowCount(), 1);
    $nodes = $rs->getArray('id');
    $this->_checkProperNesting($nodes);
    $this->_checkResultNodesArray($nodes);
  }

  function testGetChildrenFailed()
  {
    $this->assertNull($this->imp->getChildren(10000));
  }

  function testGetChildren()
  {
    $parent_node_id = $this->imp->createRootNode(array('identifier' => 'root'));
    $sub_node_id1 = $this->imp->createSubNode($parent_node_id, array('identifier' => 'test1'));
    $sub_node_id2 = $this->imp->createSubNode($parent_node_id, array('identifier' => 'test2'));
    $this->imp->createSubNode($sub_node_id1, array('identifier' => 'test0'));

    $rs =& new SimpleDbDataset($this->imp->getChildren($parent_node_id));

    $nodes = $rs->getArray('id');

    $this->assertEqual(sizeof($nodes), 2);
    $this->_checkResultNodesArray($nodes);

    $row = reset($nodes);

    $this->assertEqual($row['id'], $sub_node_id1, 'invalid parameter: id');
    $this->assertEqual($row['identifier'], 'test1', 'invalid parameter: identifier');

    $row = end($nodes);

    $this->assertEqual($row['id'], $sub_node_id2, 'invalid parameter: id');
    $this->assertEqual($row['identifier'], 'test2', 'invalid parameter: identifier');
  }

  function testCountChildrenFailed()
  {
    $this->assertFalse($this->imp->countChildren(10000));
  }

  function testCountChildren()
  {
    $parent_node_id = $this->imp->createRootNode(array('identifier' => 'root'));
    $sub_node_id1 = $this->imp->createSubNode($parent_node_id, array('identifier' => 'test1'));
    $sub_node_id2 = $this->imp->createSubNode($parent_node_id, array('identifier' => 'test2'));
    $this->imp->createSubNode($sub_node_id1, array('identifier' => 'test0'));

    $this->assertEqual(2, $this->imp->countChildren($parent_node_id));
  }

  function testGetSiblingsFailed()
  {
    $this->assertNull($this->imp->getSiblings(10000));
  }

  function testGetSiblings()
  {
    $parent_node_id = $this->imp->createRootNode(array('identifier' => 'root'));
    $sub_node_id1 = $this->imp->createSubNode($parent_node_id, array('identifier' => 'test1'));
    $sub_node_id2 = $this->imp->createSubNode($parent_node_id, array('identifier' => 'test2'));
    $this->imp->createSubNode($sub_node_id1, array('identifier' => 'test0'));

    $rs =& new SimpleDbDataset($this->imp->getSiblings($sub_node_id2));

    $nodes = $rs->getArray('id');

    $this->assertEqual(sizeof($nodes), 2);

    $row = reset($nodes);

    $this->assertEqual($row['id'], $sub_node_id1, 'invalid parameter: id');
    $this->assertEqual($row['identifier'], 'test1', 'invalid parameter: identifier');

    $row = end($nodes);

    $this->assertEqual($row['id'], $sub_node_id2, 'invalid parameter: id');
    $this->assertEqual($row['identifier'], 'test2', 'invalid parameter: identifier');
  }

  function testUpdateNodeFailed()
  {
    $this->assertFalse($this->imp->updateNode(10000, array()));
  }

  function testUpdateNode()
  {
    $node_id = $this->imp->createRootNode(array('identifier' => 'root'));

    $node = array(
      'identifier' => 'test',
      'id' => 12,
      'path' => '/0/',
      'root_id' => 0,
      'level' => 23,
      'parent_id' => 1000,
      'children' => 1000
    );

    $this->assertTrue($this->imp->updateNode($node_id, $node));

    $updated_node = $this->imp->getNode($node_id);

    $this->assertEqual($updated_node['identifier'], 'test', 'invalid parameter: identifier');
    $this->assertNotEqual($updated_node, $node, 'invalid update');
  }

  function testMoveTreeFailed()
  {
    $this->assertFalse($this->imp->moveTree(1, 1));

    $this->assertFalse($this->imp->moveTree(1, 2));

    $node_id = $this->imp->createRootNode(array('identifier' => 'root'));
    $sub_node_id = $this->imp->createSubNode($node_id, array('identifier' => 'test'));

    $this->assertFalse($this->imp->moveTree($node_id, $node_id-1));

    $this->assertFalse($this->imp->moveTree($node_id, $sub_node_id));
  }

  function testMoveTree()
  {
    $root_id = $this->imp->createRootNode(array('identifier' => 'root'));
    $sub_node_id_1 = $this->imp->createSubNode($root_id, array('identifier' => 'test'));
    $sub_node_id_1_1 = $this->imp->createSubNode($sub_node_id_1, array('identifier' => 'test'));
    $sub_node_id_1_1_1 = $this->imp->createSubNode($sub_node_id_1_1, array('identifier' => 'test'));
    $sub_node_id_1_1_2 = $this->imp->createSubNode($sub_node_id_1_1, array('identifier' => 'test'));

    $root_id_2 = $this->imp->createRootNode( array('identifier' => 'test'));
    $sub_node_id_2 = $this->imp->createSubNode($root_id_2, array('identifier' => 'test'));

    $root_node = $this->imp->getNode($root_id);
    $this->assertEqual($root_node['children'], 1, 'invalid parent parameter: children');

    $this->assertTrue($this->imp->moveTree($sub_node_id_1, $sub_node_id_2));

    $root_node = $this->imp->getNode($root_id);
    $this->assertEqual($root_node['children'], 0, 'invalid parent parameter: children');

    $sub_node_2 = $this->imp->getNode($sub_node_id_2);
    $this->assertEqual($sub_node_2['children'], 1, 'invalid parent parameter: children');

    $current_path = '/' . $root_id_2 . '/' . $sub_node_id_2 . '/';

    $sub_node_1 = $this->imp->getNode($sub_node_id_1);

    $current_path .= $sub_node_id_1 . '/';
    $this->assertEqual($sub_node_1['level'], 3, 'invalid parameter: level');
    $this->assertEqual($sub_node_1['parent_id'], $sub_node_id_2, 'invalid parameter: parent_id');
    $this->assertEqual($sub_node_1['path'], $current_path, 'invalid parameter: path');
    $this->assertEqual($sub_node_1['root_id'], $root_id_2, 'invalid parameter: root_id');

    $current_path .= $sub_node_id_1_1 . '/';
    $sub_node_1_1 = $this->imp->getNode($sub_node_id_1_1);

    $this->assertEqual($sub_node_1_1['level'], 4, 'invalid parameter: level');
    $this->assertEqual($sub_node_1_1['parent_id'], $sub_node_id_1, 'invalid parameter: parent_id');
    $this->assertEqual($sub_node_1_1['path'], $current_path , 'invalid parameter: path');
    $this->assertEqual($sub_node_1_1['root_id'], $root_id_2, 'invalid parameter: root_id');

    $sub_node_1_1_1 = $this->imp->getNode($sub_node_id_1_1_1);

    $this->assertEqual($sub_node_1_1_1['level'], 5, 'invalid parameter: level');
    $this->assertEqual($sub_node_1_1_1['parent_id'], $sub_node_id_1_1, 'invalid parameter: parent_id');
    $this->assertEqual($sub_node_1_1_1['path'], $current_path . $sub_node_id_1_1_1 . '/', 'invalid parameter: path');
    $this->assertEqual($sub_node_1_1_1['root_id'], $root_id_2, 'invalid parameter: root_id');

    $sub_node_1_1_2 = $this->imp->getNode($sub_node_id_1_1_2);

    $this->assertEqual($sub_node_1_1_2['level'], 5, 'invalid parameter: level');
    $this->assertEqual($sub_node_1_1_2['parent_id'], $sub_node_id_1_1, 'invalid parameter: parent_id');
    $this->assertEqual($sub_node_1_1_2['path'], $current_path . $sub_node_id_1_1_2 . '/', 'invalid parameter: path');
    $this->assertEqual($sub_node_1_1_2['root_id'], $root_id_2, 'invalid parameter: root_id');
  }

  function testGetSubBranchFailed()
  {
    $this->assertNull($this->imp->getSubBranch(1));
  }

  function testGetSubBranch()
  {
    $root_id = $this->imp->createRootNode(array('identifier' => 'root'));
    $sub_node_id_1 = $this->imp->createSubNode($root_id, array('identifier' => 'test'));
    $sub_node_id_1_1 = $this->imp->createSubNode($sub_node_id_1, array('identifier' => 'test'));
    $sub_node_id_1_1_1 = $this->imp->createSubNode($sub_node_id_1_1, array('identifier' => 'test'));
    $sub_node_id_1_1_2 = $this->imp->createSubNode($sub_node_id_1_1, array('identifier' => 'test'));

    //getting all
    $rs = & new SimpleDbDataset($this->imp->getSubBranch($sub_node_id_1));
    $branch = $rs->getArray('id');
    $this->assertEqual(3, sizeof($branch));
    $this->_checkResultNodesArray($branch);
    $this->_checkProperNesting($branch);

    $node = current($branch);
    $this->assertEqual($node['id'], $sub_node_id_1_1, 'invalid parameter: id');

    //getting at unlimited depth, including node
    $rs = & new SimpleDbDataset($this->imp->getSubBranch($sub_node_id_1, -1, true));
    $branch = $rs->getArray('id');
    $this->assertEqual(4, sizeof($branch));
    $this->_checkResultNodesArray($branch);
    $this->_checkProperNesting($branch);

    //getting at depth = 1
    $rs = & new SimpleDbDataset($this->imp->getSubBranch($sub_node_id_1, 1));
    $branch = $rs->getArray('id');
    $this->assertEqual(1, sizeof($branch));
    $this->_checkResultNodesArray($branch,  __LINE__);
    $this->_checkProperNesting($branch);

    //getting at depth = 1, including node
    $rs = & new SimpleDbDataset($this->imp->getSubBranch($sub_node_id_1, 1, true));
    $branch = $rs->getArray('id');
    $this->assertEqual(2, sizeof($branch));
    $this->_checkResultNodesArray($branch,  __LINE__);
    $this->_checkProperNesting($branch);
  }

  function testGetSubBranchCheckExpandedParents()
  {
    //creating subtree
    $root_id = $this->imp->createRootNode(array('identifier' => 'root'));
    $sub_node_id_1 = $this->imp->createSubNode($root_id, array('identifier' => 'test'));
    $sub_node_id_1_1 = $this->imp->createSubNode($sub_node_id_1, array('identifier' => 'test'));
    $sub_node_id_1_1_1 = $this->imp->createSubNode($sub_node_id_1_1, array('identifier' => 'test'));
    $sub_node_id_1_1_2 = $this->imp->createSubNode($sub_node_id_1_1, array('identifier' => 'test'));

    //creating second subtree to check that only one subtree uses during fetch subbranch
    $root_id_2 = $this->imp->createRootNode(array('identifier' => 'root'));
    $sub_node_id_2 = $this->imp->createSubNode($root_id_2, array('identifier' => 'test'));
    $sub_node_id_2_1 = $this->imp->createSubNode($sub_node_id_2, array('identifier' => 'test'));
    $sub_node_id_2_1_1 = $this->imp->createSubNode($sub_node_id_2_1, array('identifier' => 'test'));
    $sub_node_id_2_1_2 = $this->imp->createSubNode($sub_node_id_2_1, array('identifier' => 'test'));

    $this->imp->normalizeExpandedParents();

    $this->imp->expandNode($sub_node_id_1);

    $this->imp->expandNode($sub_node_id_2);
    $this->imp->expandNode($sub_node_id_2_1);

    //getting at unlimited depth, including node, checking expanded parents
    $rs = & new SimpleDbDataset($this->imp->getSubBranch($root_id, -1, true, true));
    $branch = $rs->getArray('id');
    $this->assertEqual(3, sizeof($branch));
    $this->_checkResultNodesArray($branch);
    $this->_checkProperNesting($branch);

    $this->imp->collapseNode($root_id);
    $this->imp->expandNode($sub_node_id_1_1);

    //the test below seems to be wrong: why should we include parent if it's collapsed?

    //getting at unlimited depth, including node, checking expanded parents
    //$rs = & new SimpleDbDataset($this->imp->getSubBranch($root_id, -1, true, true));
    //$branch = $rs->getArray('id');
    //$this->assertEqual(1, sizeof($branch));
    //$this->_checkResultNodesArray($branch);
    //$this->_checkProperNesting($branch);
  }

  function testGetNodeByPathFailed()
  {
    $root_id = $this->imp->createRootNode(array('identifier' => 'root'));
    $sub_node_id_1 = $this->imp->createSubNode($root_id, array('identifier' => '1_test'));
    $sub_node_id_1_1 = $this->imp->createSubNode($sub_node_id_1, array('identifier' => '1_1_test'));
    $sub_node_id_1_1_1 = $this->imp->createSubNode($sub_node_id_1_1, array('identifier' => '1_1_1_test'));
    $sub_node_id_1_1_2 = $this->imp->createSubNode($sub_node_id_1_1, array('identifier' => '1_1_2_test'));

    $root_id2 = $this->imp->createRootNode(array('identifier' => 'root2'));
    $sub_node_id_2 = $this->imp->createSubNode($root_id2, array('identifier' => '2_test'));

    $this->assertFalse($this->imp->getNodeByPath(''));
    $this->assertFalse($this->imp->getNodeByPath('/root///'));
    $this->assertFalse($this->imp->getNodeByPath('/root/wow/yo'));
    $this->assertFalse($this->imp->getNodeByPath('/root/2_test'));
  }

  function testGetNodeByPath()
  {
    $root_id = $this->imp->createRootNode(array('identifier' => 'root'));
    $sub_node_id_1 = $this->imp->createSubNode($root_id, array('identifier' => 'test1'));
    $sub_node_id_1_1 = $this->imp->createSubNode($sub_node_id_1, array('identifier' => 'test1'));
    $sub_node_id_1_1_1 = $this->imp->createSubNode($sub_node_id_1_1, array('identifier' => 'test1'));
    $sub_node_id_1_1_2 = $this->imp->createSubNode($sub_node_id_1_1, array('identifier' => 'test2'));

    $node = $this->imp->getNodeByPath('/root/');
    $this->assertEqual($node['id'], $root_id);
    $this->_checkResultNodesArray($node);

    $node = $this->imp->getNodeByPath('/root/test1/test1/');
    $this->assertEqual($node['id'], $sub_node_id_1_1);
    $this->_checkResultNodesArray($node,  __LINE__);

    $node = $this->imp->getNodeByPath('/root/test1/test1/test2');
    $this->assertEqual($node['id'], $sub_node_id_1_1_2);
    $this->_checkResultNodesArray($node,  __LINE__);

    $node = $this->imp->getNodeByPath('/root/test1/test1/test1/');
    $this->assertEqual($node['id'], $sub_node_id_1_1_1);
    $this->_checkResultNodesArray($node,  __LINE__);
  }

  function testGetPathToNode()
  {
    $root_id = $this->imp->createRootNode(array('identifier' => 'root'));
    $sub_node_id_1 = $this->imp->createSubNode($root_id, array('identifier' => 'test1'));
    $sub_node_id_1_1 = $this->imp->createSubNode($sub_node_id_1, array('identifier' => 'test1'));
    $sub_node_id_1_1_1 = $this->imp->createSubNode($sub_node_id_1_1, array('identifier' => 'test1'));
    $sub_node_id_1_1_2 = $this->imp->createSubNode($sub_node_id_1_1, array('identifier' => 'test2'));

    $path = $this->imp->getPathToNode($root_id, '|');
    $this->assertEqual($path, '|root');

    $path = $this->imp->getPathToNode(array('id' => $sub_node_id_1_1,
                                               'identifier' => 'test1'));
    $this->assertEqual($path, '/root/test1/test1');

    $path = $this->imp->getPathToNode($sub_node_id_1_1_2, '|');
    $this->assertEqual($path, '|root|test1|test1|test2');

    $path = $this->imp->getPathToNode(-1000000);
    $this->assertNull($path);
  }

  function testGetSubBranchByPathFailed()
  {
    $this->assertFalse($this->imp->getSubBranch(1));
  }

  function testGetNodesByIds()
  {
    $root_id = $this->imp->createRootNode(array('identifier' => 'root'));
    $sub_node_id_1 = $this->imp->createSubNode($root_id, array('identifier' => 'test1'));
    $sub_node_id_1_1 = $this->imp->createSubNode($sub_node_id_1, array('identifier' => 'test1'));
    $sub_node_id_1_1_1 = $this->imp->createSubNode($sub_node_id_1_1, array('identifier' => 'test1'));
    $sub_node_id_1_1_2 = $this->imp->createSubNode($sub_node_id_1_1, array('identifier' => 'test2'));

    $rs = & new SimpleDbDataset($this->imp->getNodesByIds(
      array(
        $root_id,
        $sub_node_id_1,
        $sub_node_id_1_1,
        $sub_node_id_1_1_2,
        $sub_node_id_1_1_1,
        -1
      )
    ));

    $nodes = $rs->getArray('id');

    $this->assertEqual(sizeof($nodes), 5);
    $this->_checkResultNodesArray($nodes,  __LINE__);

    $rs = & new SimpleDbDataset($this->imp->getNodesByIds(
      array(
        $sub_node_id_1,
        $sub_node_id_1_1,
        $sub_node_id_1_1_1,
        -1
      )
    ));

    $nodes = $rs->getArray('id');

    $this->assertEqual(sizeof($nodes), 3);
    $this->_checkResultNodesArray($nodes,  __LINE__);

    $rs = & new SimpleDbDataset($this->imp->getNodesByIds(
      array()
    ));

    $nodes = $rs->getArray();

    $this->assertEqual(sizeof($nodes), 0);
  }

  function _checkResultNodesArray($nodes, $line='')
  {
    if(isset($nodes['id']))//check for array
      $this->assertEqual($this->imp->getNode($nodes['id']), $nodes);
    else
      foreach($nodes as $id => $node)
        $this->assertEqual($this->imp->getNode($id), $node);
  }

  function _checkProperNesting($nodes, $line='')
  {
    $this->assertEqual(ComplexArray :: sortArray($nodes, array('path' => 'ASC')),
                       $nodes);

    $path = ComplexArray :: getMinColumnValue('path', $nodes, $index);
    $parent_paths[] = $this->_getParentPath($path);

    $counter = 0;
    foreach($nodes as $id => $node)
    {
      $parent_path = $this->_getParentPath($node['path']);

      $this->assertTrue(in_array($parent_path, $parent_paths),
        'path is improperly nested: ' . $node['path'] . ' , expected parent not found: ' . $parent_path . ' at line: ' . $line);

      $parent_paths[] = $node['path'];
    }
  }

  function _getParentPath($path)
  {
    preg_match('~^(.*/)[^/]+/$~', $path, $matches);
    return $matches[1];
  }
}
?>
