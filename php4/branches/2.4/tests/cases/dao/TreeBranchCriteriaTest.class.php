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
require_once(LIMB_DIR . '/core/dao/SiteObjectsDAO.class.php');
require_once(LIMB_DIR . '/core/dao/criteria/TreeBranchCriteria.class.php');
require_once(dirname(__FILE__) . '/SiteObjectsSQLBaseTest.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                        'TreeBranchCriteriaTestToolkit',
                        array('getTree'));
Mock :: generate('Tree');

class TreeBranchCriteriaTest extends SiteObjectsSQLBaseTest
{
  var $dao;

  function TreeBranchCriteriaTest()
  {
    parent :: SiteObjectsSQLBaseTest('tree branch criteria tests');
  }

  function setUp()
  {
    parent :: setUp();

    $this->dao = new SiteObjectsDAO();
  }

  // Default settings means: depth = 1, include_parent = false,
  // check_expanded_parents = false;
  function testLimitByBranchDefaultSettings()
  {
    $criteria = new TreeBranchCriteria();
    $criteria->setPath('/root');

    $this->dao->addCriteria($criteria);
    $rs =& new SimpleDbDataset($this->dao->fetch());

    $this->assertEqual($rs->getTotalRowCount(), 10);

    $record = $rs->getRow();

    $this->assertEqual($record['identifier'], 'object_1');
    $this->assertEqual($record['title'], 'object_1_title');
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
}
?>
