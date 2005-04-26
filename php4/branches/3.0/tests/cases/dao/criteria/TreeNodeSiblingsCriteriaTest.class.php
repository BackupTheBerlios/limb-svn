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
require_once(LIMB_DIR . '/core/dao/criteria/TreeNodeSiblingsCriteria.class.php');
require_once(LIMB_DIR . '/core/tree/Tree.interface.php');
require_once(LIMB_DIR . '/core/db/ComplexSelectSQL.class.php');
require_once(WACT_ROOT . '/iterator/arraydataset.inc.php');

Mock :: generate('Tree');
Mock :: generate('ComplexSelectSQL');

Mock :: generatePartial('LimbBaseToolkit',
                        'ToolkitTreeNodeSiblingsCriteriaTestVersion',
                        array('getTree'));


class TreeNodeSiblingsCriteriaTest extends LimbTestCase
{
  var $toolkit;
  var $sql;
  var $tree;

  function TreeNodeSiblingsCriteriaTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->sql = new MockComplexSelectSQL($this);
    $this->tree = new MockTree($this);

    $this->toolkit = new ToolkitTreeNodeSiblingsCriteriaTestVersion($this);
    $this->toolkit->setReturnReference('getTree', $this->tree);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->tree->tally();
    $this->sql->tally();

    Limb :: restoreToolkit();
  }

  function testPerform()
  {
    $criteria = new TreeNodeSiblingsCriteria();
    $criteria->setParentNodeId($parent_node_id = 1);

    $data = array(array('id' => $node_id1 = 10),
                  array('id' => $node_id2 = 11));
    $rs = new ArrayDataSet($data);

    $this->tree->expectOnce('getChildren', array($parent_node_id));
    $this->tree->setReturnReference('getChildren', $rs);

    $this->sql->expectOnce('addCondition', array("tree.id IN ($node_id1, $node_id2)"));

    $criteria->process($this->sql);
  }
}
?>
