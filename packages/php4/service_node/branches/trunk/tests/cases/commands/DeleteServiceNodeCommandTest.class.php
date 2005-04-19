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
require_once(LIMB_SERVICE_NODE_DIR . '/commands/DeleteServiceNodeCommand.class.php');
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

  function testPeformError()
  {
    $context = new DataSpace();

    $command = new DeleteServiceNodeCommand($field_name = 'whatever');

    $this->assertEqual($command->perform($context), LIMB_STATUS_ERROR);
  }

  function testPerformOK()
  {
    $context = new DataSpace();

    $entity = new ServiceNode();
    $entity->set('oid', $id = 1001);

    $node =& $entity->getPart('node');
    $node->set('id', $node_id = 10);

    $this->tree->expectOnce('countChildren', array($node_id));
    $this->tree->setReturnValue('countChildren', 0);

    $context->setObject($field_name = 'whatever', $entity);

    $command = new DeleteServiceNodeCommand($field_name);

    $this->assertEqual($command->perform($context), LIMB_STATUS_OK);

    $uow =& $this->toolkit->getUOW();
    $this->assertTrue($uow->isDeleted($entity));
  }

  function testPerformFailedEntityHasChildren()
  {
    $context = new DataSpace();

    $entity = new ServiceNode();
    $entity->set('oid', $id = 1001);

    $node =& $entity->getPart('node');
    $node->set('id', $node_id = 10);

    $this->tree->expectOnce('countChildren', array($node_id));
    $this->tree->setReturnValue('countChildren', 1);

    $context->setObject($field_name = 'whatever', $entity);

    $command = new DeleteServiceNodeCommand($field_name);

    $this->assertEqual($command->perform($context), LIMB_STATUS_ERROR);

    $uow =& $this->toolkit->getUOW();
    $this->assertFalse($uow->isDeleted($entity));
  }
}

?>
