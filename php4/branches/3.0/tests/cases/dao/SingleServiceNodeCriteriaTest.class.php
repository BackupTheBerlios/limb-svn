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
require_once(LIMB_DIR . '/core/dao/criteria/SingleServiceNodeCriteria.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');
require_once(LIMB_DIR . '/core/db/ComplexSelectSQL.class.php');
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                        'SingleServiceNodeCriteriaTestToolkit',
                        array('getTree'));
Mock :: generate('Tree');
Mock :: generate('ComplexSelectSQL');


class SingleServiceNodeCriteriaTest extends LimbTestCase
{
  var $dao;
  var $sql;

  function SingleServiceNodeCriteriaTest()
  {
    parent :: LimbTestCase('single service node criteria tests');
  }

  function setUp()
  {
    $this->dao = new SQLBasedDAO();
    $this->sql = new MockComplexSelectSQL($this);
    $this->sql->setReturnValue('toString', 'SELECT * from sys_object');
    $this->dao->setSQL($this->sql);
  }

  function testLimitByObjectId()
  {
    $criteria = new SingleServiceNodeCriteria();
    $criteria->setObjectId($id = 3);

    $this->dao->addCriteria($criteria);
    $this->sql->expectOnce('addCondition', array('sys_object.oid = '. $id));

    $this->dao->fetch();
  }

  function testLimitByNodeId()
  {
    $criteria = new SingleServiceNodeCriteria();
    $criteria->setNodeId($node_id = 10);

    $this->dao->addCriteria($criteria);
    $this->sql->expectOnce('addCondition', array('sys_tree.id = '. $node_id));
    $this->dao->fetch();
  }

  function testLimitByPath()
  {
    $criteria = new SingleServiceNodeCriteria();

    $criteria->setPath($path = '/some_path');
    $this->dao->addCriteria($criteria);

    $toolkit = new SingleServiceNodeCriteriaTestToolkit($this);
    $tree = new MockTree($this);
    $toolkit->setReturnReference('getTree', $tree);

    Limb :: registerToolkit($toolkit);

    $tree->expectOnce('getNodeByPath', array($path));
    $tree->setReturnValue('getNodeByPath', $node = array('id' => $node_id = 10), array($path));

    $this->sql->expectOnce('addCondition', array('sys_tree.id = '. $node_id));
    $this->dao->fetch();

    Limb :: popToolkit();
  }

  function testNoConditionsIfNoCritetiasSet()
  {
    $criteria = new SingleServiceNodeCriteria();

    $this->dao->addCriteria($criteria);
    $this->sql->expectOnce('addCondition', array('0 = 1'));
    $this->dao->fetch();
  }

  function testNoConditionsIfTreePathNotFound()
  {
    $criteria = new SingleServiceNodeCriteria();
    $criteria->setPath('/root/no_such_object');
    $this->dao->addCriteria($criteria);

    $toolkit = new SingleServiceNodeCriteriaTestToolkit($this);
    $tree = new MockTree($this);
    $toolkit->setReturnReference('getTree', $tree);

    Limb :: registerToolkit($toolkit);

    $this->sql->expectOnce('addCondition', array('0 = 1'));
    $this->dao->fetch();

    Limb :: popToolkit();
  }
}
?>
