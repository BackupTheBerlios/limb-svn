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
require_once(LIMB_DIR . '/core/commands/RegisterObjectCommand.class.php');
require_once(dirname(__FILE__) . '/simple_object.inc.php');

class RegisterObjectCommandTest extends LimbTestCase
{
  function RegisterObjectCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function testPerformError()
  {
    $context = new DataSpace();

    $toolkit =& Limb :: toolkit();

    $command = new RegisterObjectCommand($field_name = 'whatever');

    $this->assertEqual($command->perform($context), LIMB_STATUS_ERROR);
  }

  function testPerformOK()
  {
    $context = new DataSpace();

    $object = new SimpleObject();
    $object->set('title', $title = 'any title');

    $context->setObject($entity_name = 'whatever', $object);

    $toolkit =& Limb :: toolkit();

    $command = new RegisterObjectCommand($entity_name);

    $this->assertEqual($command->perform($context), LIMB_STATUS_OK);

    $uow =& $toolkit->getUOW();

    $this->assertTrue($uow->isRegistered($object));
  }
}

?>
