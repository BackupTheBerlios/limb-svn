<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: DeleteObjectCommandTest.class.php 1209 2005-04-08 14:29:41Z pachanga $
*
***********************************************************************************/
require_once(LIMB_SERVICE_NODE_DIR . '/commands/ServiceNodeDeleteCommand.class.php');
require_once(LIMB_DIR . '/core/tree/Tree.interface.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNode.class.php');

Mock :: generate('Tree');
Mock :: generatePartial('LimbBaseToolkit',
                        'ToolkitDeleteServiceNodeCommandTestVersion',
                        array('getTree'));

class DeleteServiceNodeCommandTest extends LimbTestCase
{
  var $toolkit;
  var $tree;

  function DeleteServiceNodeCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->toolkit = new ToolkitDeleteServiceNodeCommandTestVersion($this);

    $this->tree = new MockTree($this);

    $this->toolkit->setReturnReference('getTree', $this->tree);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->toolkit;
    $this->tree;

    Limb :: restoreToolkit();
  }

  function testPerformOK()
  {
    $entity = new ServiceNode();
    $entity->set('oid', $id = 1001);

    $node =& $entity->getNodePart();
    $node->set('id', $node_id = 10);

    $this->tree->expectOnce('countChildren', array($node_id));
    $this->tree->setReturnValue('countChildren', 0);

    $command = new ServiceNodeDeleteCommand($entity);

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $uow =& $this->toolkit->getUOW();
    $this->assertTrue($uow->isDeleted($entity));
  }

  function testPerformFailedEntityHasChildren()
  {
    $entity = new ServiceNode();
    $entity->set('oid', $id = 1001);

    $node =& $entity->getNodePart();
    $node->set('id', $node_id = 10);

    $this->tree->expectOnce('countChildren', array($node_id));
    $this->tree->setReturnValue('countChildren', 1);

    $command = new ServiceNodeDeleteCommand($entity);

    $this->assertEqual($command->perform(), LIMB_STATUS_ERROR);

    $uow =& $this->toolkit->getUOW();
    $this->assertFalse($uow->isDeleted($entity));
  }
}

?>
