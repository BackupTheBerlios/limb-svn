<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/commands/DeleteSimpleObjectCommand.class.php');
require_once(dirname(__FILE__) . '/simple_object.inc.php');

class DeleteSimpleObjectCommandTest extends LimbTestCase
{
  function DeleteSimpleObjectCommandTest()
  {
    parent :: LimbTestCase('delete simple cms object command test');
  }

  function setUp()
  {
    Limb :: saveToolkit();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();
  }

  function testPerformOK()
  {
    $object = new SimpleObject();
    $object->set('id', $id = 1001);

    $command = new DeleteSimpleObjectCommand($object);

    $toolkit =& Limb :: toolkit();

    $uow =& $toolkit->getUOW();
    $uow->register($object);

    $request =& $toolkit->getRequest();
    $request->set('id', $id);

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $this->assertTrue($uow->isDeleted($object));
  }

  function testPerformError()
  {
    $object = new SimpleObject();

    $command = new DeleteSimpleObjectCommand($object);

    $toolkit =& Limb :: toolkit();

    $uow =& $toolkit->getUOW();
    $uow->register($object);

    $request =& $toolkit->getRequest();
    $request->set('id', $id = 1000);

    $this->assertEqual($command->perform(), LIMB_STATUS_ERROR);

    $this->assertFalse($uow->isDeleted($object));
  }
}

?>
