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
require_once(LIMB_DIR . '/core/commands/DeleteObjectCommand.class.php');
require_once(dirname(__FILE__) . '/simple_object.inc.php');

class DeleteObjectCommandTest extends LimbTestCase
{
  function DeleteObjectCommandTest()
  {
    parent :: LimbTestCase('delete object command test');
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
    $toolkit =& Limb :: toolkit();

    $object = new SimpleObject();
    $object->set('id', $id = 1001);

    $toolkit->setProcessedObject($object);

    $command = new DeleteObjectCommand();

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $uow =& $toolkit->getUOW();
    $this->assertTrue($uow->isDeleted($object));
  }

  function testPerformError()
  {
    $object = new SimpleObject();
    $toolkit =& Limb :: toolkit();

    $command = new DeleteObjectCommand();

    $this->assertEqual($command->perform(), LIMB_STATUS_ERROR);

    $uow =& $toolkit->getUOW();
    $this->assertFalse($uow->isDeleted($object));
  }
}

?>
