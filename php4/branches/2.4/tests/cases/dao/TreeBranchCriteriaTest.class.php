<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: OneTableObjectsCriteriaTest.class.php 1068 2005-01-28 14:01:40Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/tree/Tree.interface.php');
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');
require_once(LIMB_DIR . '/core/dao/criteria/TreeBranchCriteria.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');
require_once(LIMB_DIR . '/core/tree/MaterializedPathTree.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                        'TreeBranchCriteriaTestToolkit',
                        array('getTree'));
Mock :: generate('Tree');

class TreeBranchCriteriaTest extends LimbTestCase
{
  var $dao;
  var $db;
  var $conn;
  var $root_node_id;
  var $object2node = array();

  function TreeBranchCriteriaTest()
  {
    parent :: LimbTestCase('tree branch criteria tests');
  }

  function setUp()
  {

    $this->conn =& LimbDbPool :: getConnection();
    $this->db =& new SimpleDb($this->conn);

    $this->_cleanUp();

    $this->dao = new SQLBasedDAO();
    $sql = new ComplexSelectSQL('SELECT sys_object.oid as oid %fields% FROM sys_object %tables% %where%');
    $this->dao->setSQL($sql);

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

    $this->assertEqual($rs->getTotalRowCount(), 5);

    $record = $rs->getRow();

    $this->assertEqual($record['identifier'], 'object_1');
  }

  function testNoObjectsIfNoNodesAreFound()
  {
    $criteria = new TreeBranchCriteria();
    $criteria->setPath('/no_such_path');

    $this->dao->addCriteria($criteria);
    $rs =& new SimpleDbDataset($this->dao->fetch());
    $this->assertEqual($rs->getTotalRowCount(), 0);
  }

  function testParamsPassedOk()
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

    Limb :: popToolkit();
  }

  function _insertNodeRecords()
  {
    $tree = new MaterializedPathTree();

    $values['identifier'] = 'root';
    $this->root_node_id = $tree->createRootNode($values, false, true);

    $data = array();
    for($i = 1; $i <= 5; $i++)
    {
      $values['identifier'] = 'object_' . $i;
      $this->object2node[$i] = $tree->createSubNode($this->root_node_id, $values);
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
