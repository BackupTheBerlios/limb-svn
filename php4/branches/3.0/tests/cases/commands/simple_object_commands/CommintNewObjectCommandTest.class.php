<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CreateSimpleObjectCommandTest.class.php 1165 2005-03-16 14:28:14Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/commands/CommitNewObjectCommand.class.php');
require_once(dirname(__FILE__) . '/simple_object.inc.php');

class CommitNewObjectCommandTest extends LimbTestCase
{
  function CommitNewObjectCommandTest()
  {
    parent :: LimbTestCase('commit new object command test');
  }

  function setUp()
  {
    Limb :: saveToolkit();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();
  }

  function testPerform()
  {
    $object = new SimpleObject();
    $object->set('title', $title = 'any title');

    $toolkit =& Limb :: toolkit();
    $toolkit->setProcessedObject($object);

    $command = new CommitNewObjectCommand();

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $uow =& $toolkit->getUOW();

    $this->assertTrue($uow->isRegistered($object));
  }
}

?>
