<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: TreeBranchCriteriaTest.class.php 1209 2005-04-08 14:29:41Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/DAO/SQLBasedDAO.class.php');
require_once(LIMB_DIR . '/core/DAO/criteria/TreeRootNodesCriteria.class.php');

Mock :: generatePartial('SQLBasedDAO',
                        'SQLBasedDAOTRNCTestVersion',
                        array('_initSQL'));

class TreeRootNodesCriteriaTest extends LimbTestCase
{
  var $dao;
  var $sql;
  var $db;
  var $conn;
  var $root_node_id;
  var $object2node = array();

  function TreeRootNodesCriteriaTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $this->conn =& $toolkit->getDbConnection();
    $this->db =& new SimpleDb($this->conn);

    $this->_cleanUp();

    $this->dao = new SQLBasedDAOTRNCTestVersion($this);

    $sql = 'SELECT sys_object.oid as oid, '.
           'tree.id as node_id, tree.parent_id as parent_node_id, tree.identifier %fields% '.
           ' FROM sys_object, sys_tree as tree, sys_object_to_node %tables% '.
           ' WHERE sys_object_to_node.oid = sys_object.oid AND sys_object_to_node.node_id = tree.id'.
           ' %where%';

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

  function testPerform()
  {
    $criteria = new TreeRootNodesCriteria();

    $this->dao->addCriteria($criteria);
    $rs =& new SimpleDbDataset($this->dao->fetch());
    $this->assertEqual($rs->getTotalRowCount(), 5);

    $rs->rewind();

    $record =& $rs->current();

    $this->assertEqual($record->get('node_id'), $this->object2node[1]);
  }

  function _insertNodeRecords()
  {
    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    $values = array();
    for($i = 1; $i <= 5; $i++)
    {
      $values['identifier'] = 'object_' . $i;
      $this->object2node[$i] = $tree->createRootNode($values, false, true);
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
