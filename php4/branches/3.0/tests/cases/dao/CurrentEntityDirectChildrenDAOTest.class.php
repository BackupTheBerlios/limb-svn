<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: ImageObjectsDAOTest.class.php 1093 2005-02-07 15:17:20Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/dao/CurrentEntityDirectChildrenDAO.class.php');
include_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');
require_once(LIMB_DIR . '/core/entity/Entity.class.php');
require_once(LIMB_DIR . '/core/NodeConnection.class.php');
require_once(LIMB_DIR . '/core/dao/criteria/TreeNodeSiblingsCriteria.class.php');

Mock :: generate('SQLBasedDAO');

Mock :: generate('TreeNodeSiblingsCriteria');

Mock :: generatePartial('CurrentEntityDirectChildrenDAO',
                        'CurrentEntityDirectChildrenDAOTestVersion',
                        array('getTreeNodeSiblingsCriteria'));

class CurrentEntityDirectChildrenDAOTest extends LimbTestCase
{
  function CurrentEntityDirectChildrenDAOTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    Limb :: saveToolkit();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();
  }

  function testFetch()
  {
    $entity = new Entity();
    $node = new NodeConnection();
    $node->set('id', $id = 10);
    $entity->registerPart('node', $node);

    $toolkit =& Limb :: toolkit();
    $toolkit->setCurrentEntity($entity);

    $result = new PagedArrayDataset(array());

    $decorated_dao = new MockSQLBasedDAO($this);
    $decorated_dao->expectOnce('addCriteria');
    $decorated_dao->expectOnce('fetch');

    $data = array(array('whatever1'),
                  array('whatever2'));

    $rs = new PagedArrayDataset($data);
    $decorated_dao->setReturnReference('fetch', $rs);

    $criteria = new MockTreeNodeSiblingsCriteria($this);
    $criteria->expectOnce('setParentNodeId', array($id));

    $dao = new CurrentEntityDirectChildrenDAOTestVersion($this);
    $dao->CurrentEntityDirectChildrenDAO($decorated_dao);
    $dao->setReturnReference('getTreeNodeSiblingsCriteria', $criteria);

    $this->assertNotNull($dao->fetch());

    $decorated_dao->tally();

    $criteria->tally();
  }

  function testGetTreeBranchCriteria()
  {
    $dao = new CurrentEntityDirectChildrenDAO(new SQLBasedDAO());
    $this->assertIsA($dao->getTreeNodeSiblingsCriteria(), 'TreeNodeSiblingsCriteria');
  }

}

?>