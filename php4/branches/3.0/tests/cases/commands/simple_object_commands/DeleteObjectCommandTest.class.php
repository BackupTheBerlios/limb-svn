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

  function testPeformError()
  {
    $toolkit =& Limb :: toolkit();

    $context = new DataSpace();

    $command = new DeleteObjectCommand($field_name = 'whatever');

    $this->assertEqual($command->perform($context), LIMB_STATUS_ERROR);
  }

  function testPerformOK()
  {
    $toolkit =& Limb :: toolkit();

    $context = new DataSpace();

    $object = new SimpleObject();
    $object->set('id', $id = 1001);

    $context->setObject($field_name = 'whatever', $object);

    $command = new DeleteObjectCommand($field_name);

    $this->assertEqual($command->perform($context), LIMB_STATUS_OK);

    $uow =& $toolkit->getUOW();
    $this->assertTrue($uow->isDeleted($object));
  }
}

?>
