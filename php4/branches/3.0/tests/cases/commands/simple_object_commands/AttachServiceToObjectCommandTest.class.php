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
require_once(LIMB_DIR . '/core/commands/AttachServiceToObjectCommand.class.php');
require_once(dirname(__FILE__) . '/simple_object.inc.php');
require_once(LIMB_DIR . '/core/Service.class.php');

class AttachServiceToObjectCommandTest extends LimbTestCase
{
  function AttachServiceToObjectCommandTest()
  {
    parent :: LimbTestCase('attach service to object command test');
  }

  function testPerform()
  {
    $toolkit =& Limb :: toolkit();

    $object = new Service();
    $toolkit->setProcessedObject($object);

    $command = new AttachServiceToObjectCommand($service_name = 'Test');

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $service =& $object->getService();
    $this->assertEqual($service->getName(), $service_name);
  }

  function testPerformFailedObjectIsNotAService()
  {
    $toolkit =& Limb :: toolkit();

    $object = new Object();
    $toolkit->setProcessedObject($object);

    $command = new AttachServiceToObjectCommand($service_name = 'Test');

    $this->assertEqual($command->perform(), LIMB_STATUS_ERROR);
  }
}

?>
