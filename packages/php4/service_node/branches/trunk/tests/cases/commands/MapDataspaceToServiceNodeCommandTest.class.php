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
require_once(LIMB_SERVICE_NODE_DIR . '/commands/MapDataspaceToServiceNodeCommand.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/tree/Tree.interface.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNode.class.php');

Mock :: generate('Tree');
Mock :: generatePartial('LimbBaseToolkit',
                        'LimbBaseToolkitMapDataspaceToServiceNodeCommandTestVersion',
                        array('getTree'));

class MapDataspaceToServiceNodeCommandTest extends LimbTestCase
{
  var $toolkit;
  var $tree;

  function MapDataspaceToServiceNodeCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->tree = new MockTree($this);

    $this->toolkit = new LimbBaseToolkitMapDataspaceToServiceNodeCommandTestVersion($this);
    $this->toolkit->setReturnReference('getTree', $this->tree);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->tree->tally();

    Limb :: restoreToolkit();
  }

  function testPerformOkAndSwitchPath()
  {
    $toolkit =& Limb :: toolkit();

    $dataspace =& $toolkit->getDataspace();
    $dataspace->set('path', $path = 'Some path'); // will change parent_node_id
    $dataspace->set('parent_node_id', 100); // will be changed

    $dataspace->set('service_name', $service_name = 'some service name');
    $dataspace->set('title', $title = 'some title');
    $dataspace->set('identifier', $identifier = 'some identifier');

    $this->tree->expectOnce('getNodeByPath', array($path));
    $this->tree->setReturnValue('getNodeByPath', $node = array('id' => $new_parent_node_id = 101));

    $entity = new ServiceNode();

    $context = new DataSpace();
    $context->setObject($entity_field_name = 'entity', $entity);

    $command = new MapDataspaceToServiceNodeCommand($entity_field_name);

    $this->assertEqual($command->perform($context), LIMB_STATUS_OK);

    $node =& $entity->getPart('node');
    $this->assertEqual($node->get('parent_id'), $new_parent_node_id);
    $this->assertEqual($node->get('identifier'), $identifier);

    $service =& $entity->getPart('service');
    $this->assertEqual($service->get('name'), $service_name);
    $this->assertEqual($service->get('title'), $title);
  }
}

?>