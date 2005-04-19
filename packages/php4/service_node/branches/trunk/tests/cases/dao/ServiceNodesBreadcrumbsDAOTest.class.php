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
require_once(LIMB_SERVICE_NODE_DIR . '/dao/ServiceNodesBreadcrumbsDAO.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNodeLocator.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNodePackageToolkit.class.php');
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');
require_once(LIMB_DIR . '/core/tree/Tree.interface.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/entity/Entity.class.php');
require_once(LIMB_DIR . '/core/NodeConnection.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                        'LimbBaseToolkitServiceNodesBreadcrumbsDAOTestVersion',
                        array('getTree',
                              'createDAO'));

Mock :: generate('SQLBasedDAO');
Mock :: generate('Tree');
Mock :: generate('ServiceNodeLocator');
Mock :: generate('ServiceNodePackageToolkit');

class ServiceNodesBreadcrumbsDAOTest extends LimbTestCase
{
  var $db;
  var $tree;
  var $uow;
  var $service_node_toolkit;
  var $toolkit;

  function ServiceNodesBreadcrumbsDAOTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->tree = new MockTree($this);

    $this->toolkit = new LimbBaseToolkitServiceNodesBreadcrumbsDAOTestVersion($this);
    $this->toolkit->setReturnReference('getTree', $this->tree);

    Limb :: registerToolkit($this->toolkit);

    $this->locator = new MockServiceNodeLocator($this);
    $this->service_node_toolkit = new MockServiceNodePackageToolkit($this);
    $this->service_node_toolkit->setReturnReference('getServiceNodeLocator', $this->locator);

    Limb :: registerToolkit($this->service_node_toolkit, 'service_node_toolkit');
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->tree->tally();
    $this->locator->tally();
    $this->service_node_toolkit->tally();

    Limb :: restoreToolkit();
    Limb :: restoreToolkit('service_node_toolkit');
  }

  function testFetch()
  {
    $entity = new Entity();
    $node = new NodeConnection();
    $node->set('id', $node_id = 10);
    $entity->registerPart('node', $node);

    $this->locator->expectOnce('getCurrentServiceNode');
    $this->locator->setReturnReference('getCurrentServiceNode', $entity);

    $this->tree->expectOnce('getParents', array($node_id));

    $result = new PagedArrayDataset(array(array('id' => $parent_node_id1 = 50),
                                          array('id' => $parent_node_id2 = 51)));

    $this->tree->setReturnValue('getParents', $result);

    $service_node_dao = new MockSQLBasedDAO($this);

    $criteria = new SimpleConditionCriteria("tree.id IN ($parent_node_id1,$parent_node_id2,$node_id)");
    $service_node_dao->expectOnce('addCriteria', array($criteria));
    $service_node_dao->expectOnce('fetch');
    $service_node_dao->setReturnValue('fetch', $result = 'whatever');

    $this->toolkit->setReturnReference('createDAO', $service_node_dao, array('ServiceNodeDAO'));

    $dao = new ServiceNodesBreadcrumbsDAO();

    $this->assertEqual($dao->fetch(), $result);

    $service_node_dao->tally();
  }
}

?>