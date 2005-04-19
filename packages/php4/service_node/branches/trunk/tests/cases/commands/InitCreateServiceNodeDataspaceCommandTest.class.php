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
require_once(LIMB_SERVICE_NODE_DIR . '/commands/InitCreateServiceNodeDataspaceCommand.class.php');
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNodeLocator.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNodePackageToolkit.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNode.class.php');

Mock :: generate('ServiceNodeLocator');
Mock :: generate('ServiceNodePackageToolkit');

class InitCreateServiceNodeDataspaceCommandTest extends LimbTestCase
{
  var $toolkit;

  function InitCreateServiceNodeDataspaceCommandTest()
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
    $this->toolkit->tally();
    $this->locator->tally();

    Limb :: restoreToolkit('service_node_toolkit');
  }

  function testPerformFailed()
  {
    $command = new InitCreateServiceNodeDataspaceCommand();
    $this->assertEqual($command->perform($context), LIMB_STATUS_ERROR);
  }

  function testPerformOk()
  {
    $entity = new ServiceNode();
    $node =& $entity->getPart('node');
    $node->set('id', $parent_node_id = 100);

    $this->locator->expectOnce('getCurrentServiceNode');
    $this->locator->setReturnReference('getCurrentServiceNode', $entity);

    $command = new InitCreateServiceNodeDataspaceCommand();

    $context = new DataSpace();

    $this->assertEqual($command->perform($context), LIMB_STATUS_OK);

    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    $this->assertEqual($dataspace->get('parent_node_id'), $parent_node_id);
  }
}

?>