<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: ImageObjectsDAOTest.class.php 1093 2005-02-07 15:17:20Z pachanga $
*
***********************************************************************************/
require_once(LIMB_SERVICE_NODE_DIR . '/commands/InitServiceNodeCommand.class.php');
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');

Mock :: generatePartial('InitServiceNodeCommand',
                        'InitServiceNodeCommandTestVersion',
                        array('getLocator'));

Mock :: generate('ServiceNodeLocator');

class InitServiceNodeCommandTest extends LimbTestCase
{
  function InitServiceNodeCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
  }

  function tearDown()
  {
  }

  function testPerformFailed()
  {
    $context = new DataSpace();

    $command = new InitServiceNodeCommandTestVersion($this);
    $command->InitServiceNodeCommand($entity_field_name = 'entity');

    $locator = new MockServiceNodeLocator($this);
    $command->setReturnReference('getLocator', $locator);

    $locator->expectOnce('getCurrentServiceNode');
    $locator->setReturnValue('getCurrentServiceNode', null);

    $this->assertEqual($command->perform($context), LIMB_STATUS_ERROR);

    $this->assertFalse($context->getObject($entity_field_name));
  }

  function testPerformOk()
  {
    $context = new DataSpace();

    $command = new InitServiceNodeCommandTestVersion($this);
    $command->InitServiceNodeCommand($entity_field_name = 'entity');

    $locator = new MockServiceNodeLocator($this);
    $command->setReturnReference('getLocator', $locator);

    $entity = new Object();

    $locator->expectOnce('getCurrentServiceNode');
    $locator->setReturnReference('getCurrentServiceNode', $entity);

    $this->assertEqual($command->perform($context), LIMB_STATUS_OK);

    $this->assertEqual($context->getObject($entity_field_name), $entity);
  }

  // Have to test this method since it was partially mocked
  function testGetLocator()
  {
    $command = new InitServiceNodeCommand('whatever');
    $this->assertIsA($command->getLocator(), 'ServiceNodeLocator');
  }
}

?>