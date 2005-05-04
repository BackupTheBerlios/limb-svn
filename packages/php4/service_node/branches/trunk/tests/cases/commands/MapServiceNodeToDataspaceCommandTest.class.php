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
require_once(LIMB_SERVICE_NODE_DIR . '/commands/MapServiceNodeToDataspaceCommand.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNode.class.php');

class MapServiceNodeToDataspaceCommandTest extends LimbTestCase
{
  function MapServiceNodeToDataspaceCommandTest()
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

  function testPerformOk()
  {
    $service_node = new ServiceNode();
    $node =& $service_node->getPart('node');
    $service =& $service_node->getPart('service');

    $node->set('id', $node_id = 50);
    $node->set('parent_id', $parent_id = 100);
    $node->set('identifier', $identifier = 'test identifier');

    $service->set('name', $service_name = 'test service');
    $service->set('title', $title = 'test title');

    $command = new MapServiceNodeToDataspaceCommand($service_node);
    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    $this->assertEqual($dataspace->get('title'), $title);
    $this->assertEqual($dataspace->get('node_id'), $node_id);
    $this->assertEqual($dataspace->get('parent_node_id'), $parent_id);
    $this->assertEqual($dataspace->get('identifier'), $identifier);
    $this->assertEqual($dataspace->get('service_name'), $service_name);
  }
}

?>