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
require_once(LIMB_SERVICE_NODE_DIR . '/dao/CurrentServiceNodeDAO.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNodePackageToolkit.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNodeLocator.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNode.class.php');

Mock :: generate('ServiceNodePackageToolkit');
Mock :: generate('ServiceNodeLocator');

class CurrentServiceNodeDAOTest extends LimbTestCase
{
  var $toolkit;
  var $locator;

  function CurrentServiceNodeDAOTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->toolkit = new MockServiceNodePackageToolkit($this);
    $this->locator = new MockServiceNodeLocator($this);
    $this->toolkit->setReturnReference('getServiceNodeLocator', $this->locator);

    Limb :: registerToolkit($this->toolkit, 'service_node_toolkit');
  }

  function tearDown()
  {
    $this->locator->tally();
    $this->toolkit->tally();

    Limb :: restoreToolkit('service_node_toolkit');
  }

  function testFetch()
  {
    $entity = new ServiceNode();
    $entity->set('oid', $id = 10);

    $node =& $entity->getPart('node');
    $node->set('identifier', 'identifier1');

    $this->locator->expectOnce('getCurrentServiceNode');
    $this->locator->setReturnReference('getCurrentServiceNode', $entity);

    $dao = new CurrentServiceNodeDAO();

    $record =& $dao->fetch();

    $this->assertEqual($record->get('oid'), $id);
    $this->assertEqual($record->get('_node_identifier'), 'identifier1');
  }
}

?>