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
require_once(LIMB_DIR . '/core/dao/SiteObjectsDAO.class.php');
require_once(LIMB_DIR . '/core/dao/criteria/SingleSiteObjectCriteria.class.php');
require_once(dirname(__FILE__) . '/SiteObjectsSQLBaseTest.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                        'SingleSiteObjectCriteriaTestToolkit',
                        array('getTree'));
Mock :: generate('Tree');

class SingleSiteObjectCriteriaTest extends SiteObjectsSQLBaseTest
{
  var $dao;

  function SingleSiteObjectCriteriaTest()
  {
    parent :: SiteObjectsSQLBaseTest('single site object criteria tests');
  }

  function setUp()
  {
    parent :: setUp();

    $this->dao = new SiteObjectsDAO();
  }

  function testLimitByObjectId()
  {
    $criteria = new SingleSiteObjectCriteria();
    $criteria->setObjectId($id = 3);

    $this->dao->addCriteria($criteria);
    $rs =& new SimpleDbDataset($this->dao->fetch());

    $this->assertEqual($rs->getTotalRowCount(), 1);

    $record = $rs->getRow();

    $this->assertEqual($record['identifier'], 'object_3');
    $this->assertEqual($record['title'], 'object_3_title');
  }

  function testLimitByNodeId()
  {
    $criteria = new SingleSiteObjectCriteria();
    $criteria->setNodeId($this->object2node[4]);

    $this->dao->addCriteria($criteria);
    $rs =& new SimpleDbDataset($this->dao->fetch());

    $this->assertEqual($rs->getTotalRowCount(), 1);

    $record = $rs->getRow();

    $this->assertEqual($record['identifier'], 'object_4');
    $this->assertEqual($record['title'], 'object_4_title');
  }

  function testLimitByPath()
  {
    $criteria = new SingleSiteObjectCriteria();
    $criteria->setPath('/root/object_2');

    $this->dao->addCriteria($criteria);
    $rs =& new SimpleDbDataset($this->dao->fetch());

    $this->assertEqual($rs->getTotalRowCount(), 1);

    $record = $rs->getRow();

    $this->assertEqual($record['identifier'], 'object_2');
    $this->assertEqual($record['title'], 'object_2_title');
  }

  function testNoLimitsSet()
  {
    $criteria = new SingleSiteObjectCriteria();

    $this->dao->addCriteria($criteria);
    $rs =& new SimpleDbDataset($this->dao->fetch());

    $this->assertEqual($rs->getTotalRowCount(), 0);
  }

  function testTreePathNotFound()
  {
    $criteria = new SingleSiteObjectCriteria();
    $criteria->setPath('/root/no_such_object');

    $this->dao->addCriteria($criteria);
    $rs =& new SimpleDbDataset($this->dao->fetch());

    $this->assertEqual($rs->getTotalRowCount(), 0);
  }
}
?>
