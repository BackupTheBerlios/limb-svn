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
require_once(LIMB_DIR . '/core/commands/InitializeNewObjectCommand.class.php');
require_once(dirname(__FILE__) . '/simple_object.inc.php');

class InitializeNewObjectCommandTest extends LimbTestCase
{
  function InitializeNewObjectCommandTest()
  {
    parent :: LimbTestCase('initialize new object command test');
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
    $command = new InitializeNewObjectCommand($handle);

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $toolkit =& Limb :: toolkit();
    $object =& $toolkit->getProcessedObject();

    $this->assertIsA($object, 'SimpleObject');
  }
}

?>
