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
require_once(LIMB_DIR . '/core/DAO/CurrentEntityDirectChildrenDAO.class.php');
include_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');
require_once(LIMB_DIR . '/core/DAO/SQLBasedDAO.class.php');
require_once(LIMB_DIR . '/core/entity/Entity.class.php');
require_once(LIMB_DIR . '/core/NodeConnection.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/tree/Tree.interface.php');
require_once(LIMB_DIR . '/core/DAO/criteria/TreeBranchCriteria.class.php');

Mock :: generate('SQLBasedDAO');

Mock :: generatePartial('LimbBaseToolkit',
                        'LimbBaseToolkitCurrentEntityDirectChildrenDAOTestVersion',
                        array('getTree'));

Mock :: generate('TreeBranchCriteria');
Mock :: generate('Tree');

Mock :: generatePartial('CurrentEntityDirectChildrenDAO',
                        'CurrentEntityDirectChildrenDAOTestVersion',
                        array('getTreeBranchCriteria'));

class CurrentEntityDirectChildrenDAOTest extends LimbTestCase
{
  var $db;
  var $toolkit;
  var $tree;

  function CurrentEntityDirectChildrenDAOTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->tree = new MockTree($this);

    $this->toolkit = new LimbBaseToolkitCurrentEntityDirectChildrenDAOTestVersion($this);
    $this->toolkit->setReturnReference('getTree', $this->tree);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->tree->tally();

    Limb :: restoreToolkit();
  }

  function testFetch()
  {
    $entity = new Entity();
    $node = new NodeConnection();
    $node->set('id', $id = 10);
    $entity->registerPart('node', $node);

    $this->toolkit->setCurrentEntity($entity);

    $this->tree->expectOnce('getPathToNode', array($id));
    $this->tree->setReturnValue('getPathToNode', $path = 'whatever');

    $result = new PagedArrayDataset(array());

    $decorated_dao = new MockSQLBasedDAO($this);
    $decorated_dao->expectOnce('addCriteria');
    $decorated_dao->expectOnce('fetch');

    $data = array(array('whatever1'),
                  array('whatever2'));

    $rs = new PagedArrayDataset($data);
    $decorated_dao->setReturnReference('fetch', $rs);

    $criteria = new MockTreeBranchCriteria($this);
    $criteria->expectOnce('setPath', array($path));

    $dao = new CurrentEntityDirectChildrenDAOTestVersion($this);
    $dao->CurrentEntityDirectChildrenDAO($decorated_dao);
    $dao->setReturnReference('getTreeBranchCriteria', $criteria);

    $this->assertIsA($dao->fetch(), 'ChildItemsPathAssignerRecordSet');

    $decorated_dao->tally();

    $criteria->tally();
  }

  function testGetTreeBranchCriteria()
  {
    $dao = new CurrentEntityDirectChildrenDAO(new SQLBasedDAO());
    $this->assertIsA($dao->getTreeBranchCriteria(), 'TreeBranchCriteria');
  }

}

?>