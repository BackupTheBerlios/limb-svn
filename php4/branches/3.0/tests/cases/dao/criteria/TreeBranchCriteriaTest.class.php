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
require_once(LIMB_DIR . '/core/tree/Tree.interface.php');
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');
require_once(LIMB_DIR . '/core/dao/criteria/TreeBranchCriteria.class.php');
require_once(LIMB_DIR . '/core/tree/MaterializedPathTree.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                        'TreeBranchCriteriaTestToolkit',
                        array('getTree'));

Mock :: generatePartial('SQLBasedDAO',
                        'SQLBasedDAOTBCTestVersion',
                        array('_initSQL'));

Mock :: generate('Tree');

class TreeBranchCriteriaTest extends LimbTestCase
{
  var $dao;
  var $sql;
  var $db;
  var $conn;
  var $root_node_id;
  var $object2node = array();

  function TreeBranchCriteriaTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $this->conn =& $toolkit->getDbConnection();
    $this->db =& new SimpleDb($this->conn);

    $this->_cleanUp();

    $this->dao = new SQLBasedDAOTBCTestVersion($this);

    $sql = 'SELECT sys_object.oid as oid, '.
           'tree.id as node_id, tree.parent_id as parent_node_id, tree.identifier %fields% '.
           ' FROM sys_object, sys_tree as tree, sys_object_to_node %tables% '.
           ' WHERE sys_object_to_node.oid = sys_object.oid AND sys_object_to_node.node_id = tree.id'.
           ' %where% %order%';

    $this->sql = new ComplexSelectSQL($sql);
    $this->dao->setReturnReference('_initSQL', $this->sql);
    $this->dao->SQLBasedDAO();

    $this->_insertNodeRecords();
    $this->_insertObjectToNodeRecords();
    $this->_insertObjectRecords();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_tree');
    $this->db->delete('sys_object_to_node');
    $this->db->delete('sys_object');
  }

  // Default settings means: depth = 1, include_parent = false,
  // check_expanded_parents = false;
  function testLimitByBranchDefaultSettings()
  {
    $criteria = new TreeBranchCriteria();
    $criteria->setPath('/root');

    $this->dao->addCriteria($criteria);

    $rs =& new SimpleDbDataset($this->dao->fetch());

    $this->assertEqual($rs->getTotalRowCount(), 3);

    $record = $rs->getRow();

    $this->assertEqual($record['identifier'], 'object_1');
  }

  function testOrderByPath()
  {
    $criteria = new TreeBranchCriteria();
    $criteria->setPath('/root');
    $criteria->setDepth(2);

    $this->dao->addCriteria($criteria);

    $rs =& new SimpleDbDataset($this->dao->fetch());

    $this->assertEqual($rs->getTotalRowCount(), 5);

    $result = $rs->getArray();

    $i = 0;
    $this->assertEqual($result[$i++]['identifier'], 'object_1');
    $this->assertEqual($result[$i++]['identifier'], 'object_2');
    $this->assertEqual($result[$i++]['identifier'], 'object_4');
    $this->assertEqual($result[$i++]['identifier'], 'object_5');
    $this->assertEqual($result[$i++]['identifier'], 'object_3');

  }

  function testNoObjectsIfNoNodesAreFound()
  {
    $criteria = new TreeBranchCriteria();
    $criteria->setPath('/no_such_path');

    $this->dao->addCriteria($criteria);
    $rs =& new SimpleDbDataset($this->dao->fetch());
    $this->assertEqual($rs->getTotalRowCount(), 0);
  }

  function testParamsPassedOkToTree()
  {
    $toolkit = new TreeBranchCriteriaTestToolkit($this);
    $tree = new MockTree($this);

    $toolkit->setReturnReference('getTree', $tree);

    Limb :: registerToolkit($toolkit);

    $criteria = new TreeBranchCriteria();
    $criteria->setPath('/root');
    $criteria->setCheckExpandedParents(true);
    $criteria->setIncludeParent(true);
    $criteria->setDepth(10);

    $expected = array('/root', 10, true, true);
    $tree->expectOnce('getSubBranchByPath', $expected);
    $tree->setReturnValue('getSubBranchByPath', false, $expected);

    $this->dao->addCriteria($criteria);
    $this->dao->fetch();

    $tree->tally();
    $toolkit->tally();

    Limb :: restoreToolkit();
  }

  function _insertNodeRecords()
  {
    $tree = new MaterializedPathTree();

    $values['identifier'] = 'root';
    $this->root_node_id = $tree->createRootNode($values, false, true);

    $data = array();
    for($i = 1; $i <= 3; $i++)
    {
      $values['identifier'] = 'object_' . $i;
      $this->object2node[$i] = $tree->createSubNode($this->root_node_id, $values);
    }

    for(; $i <= 5; $i++)
    {
      $values['identifier'] = 'object_' . $i;
      $this->object2node[$i] = $tree->createSubNode($this->object2node[2], $values);
    }
  }

  function _insertObjectRecords()
  {
    $toolkit =& Limb :: toolkit();
    $table =& $toolkit->createDBTable('SysObject');

    // Insert real records
    for($i = 1; $i <= 5; $i++)
    {
      $values['oid'] = $i;
      $values['class_id'] = 150;
      $table->insert($values);
    }
  }

  function _insertObjectToNodeRecords()
  {
    $toolkit =& Limb :: toolkit();
    $table =& $toolkit->createDBTable('SysObject2Node');

    // Insert real records
    for($i = 1; $i <= 5; $i++)
    {
      $values['node_id'] = $this->object2node[$i];
      $values['oid'] = $i;
      $table->insert($values);
    }

    // Insert fake records
    for($i = 1; $i <= 5; $i++)
    {
      $values['node_id'] = $this->object2node[$i] + 10;
      $values['oid'] = $i;
      $table->insert($values);
    }
  }
}
?>
