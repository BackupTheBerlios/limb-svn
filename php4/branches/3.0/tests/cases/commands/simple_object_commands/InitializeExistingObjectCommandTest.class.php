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
require_once(LIMB_DIR . '/core/commands/InitializeExistingObjectCommand.class.php');
require_once(dirname(__FILE__) . '/simple_object.inc.php');

class InitializeExistingObjectCommandTest extends LimbTestCase
{
  function InitializeExistingObjectCommandTest()
  {
    parent :: LimbTestCase('initialize existing object command test');
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
    $handle = new Handle('SimpleObject');
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('id', $id = 10);

    $object = new SimpleObject();
    $object->set('id', $id);

    $uow =& $toolkit->getUOW();

    $uow->register($object);

    $command = new InitializeExistingObjectCommand($handle);

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $toolkit =& Limb :: toolkit();
    $object =& $toolkit->getProcessedObject();

    $this->assertEqual($object->get('id'), $id);
  }
}

?>
