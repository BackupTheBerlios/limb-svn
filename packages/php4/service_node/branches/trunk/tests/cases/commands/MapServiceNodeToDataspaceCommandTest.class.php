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
    $node = new NodeConnection();
    $node->set('id', $node_id = 50);
    $node->set('parent_id', $parent_id = 100);
    $node->set('identifier', $identifier = 'test identifier');

    $service_location = new ServiceLocation();
    $service_location->set('name', $service_name = 'test service');
    $service_location->set('title', $title = 'test title');

    $entity = new Entity();
    $entity->registerPart('node', $node);
    $entity->registerPart('service', $service_location);

    $context = new Dataspace();
    $context->setObject($entity_field_name = 'entity', $entity);

    $command = new MapServiceNodeToDataspaceCommand($entity_field_name);
    $this->assertEqual($command->perform($context), LIMB_STATUS_OK);

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