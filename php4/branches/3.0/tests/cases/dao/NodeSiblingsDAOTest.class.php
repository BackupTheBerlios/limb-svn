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
require_once(LIMB_DIR . '/core/dao/NodeSiblingsDAO.class.php');
include_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');
require_once(LIMB_DIR . '/core/dao/RequestResolverResultDAO.class.php');
require_once(LIMB_DIR . '/core/dao/criteria/TreeNodeSiblingsCriteria.class.php');

Mock :: generate('SQLBasedDAO');
Mock :: generate('RequestResolverResultDAO');
Mock :: generate('TreeNodeSiblingsCriteria');

Mock :: generatePartial('NodeSiblingsDAO',
                        'NodeSiblingsDAOTestVersion',
                        array('getTreeNodeSiblingsCriteria'));

class NodeSiblingsDAOTest extends LimbTestCase
{
  function NodeSiblingsDAOTest()
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
    $entity = new DataSpace();
    $entity->set('_node_id', $id = 10);

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

    $node_dao = new MockRequestResolverResultDAO($this);
    $node_dao->expectOnce('fetch');
    $node_dao->setReturnReference('fetch', $entity);

    $dao = new NodeSiblingsDAOTestVersion($this);
    $dao->NodeSiblingsDAO($decorated_dao, $node_dao);
    $dao->setReturnReference('getTreeNodeSiblingsCriteria', $criteria);

    $this->assertNotNull($dao->fetch());

    $decorated_dao->tally();
    $node_dao->tally();

    $criteria->tally();
  }

  function testGetTreeBranchCriteria()
  {
    $dao = new NodeSiblingsDAO(new SQLBasedDAO(), new RequestResolverResultDAO('whatever'));
    $this->assertIsA($dao->getTreeNodeSiblingsCriteria(), 'TreeNodeSiblingsCriteria');
  }

}

?>