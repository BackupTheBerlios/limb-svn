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
require_once(LIMB_DIR . '/core/commands/RegisterObjectCommand.class.php');

class RegisterObjectCommandTest extends LimbTestCase
{
  function RegisterObjectCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function testPerformOK()
  {
    $object = new Object();
    $object->__class_name = 'Object';
    $object->set('title', $title = 'any title');

    $toolkit =& Limb :: toolkit();

    $command = new RegisterObjectCommand($object);

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $uow =& $toolkit->getUOW();

    $this->assertTrue($uow->isNew($object));
  }
}

?>
