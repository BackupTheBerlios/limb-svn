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
require_once(LIMB_DIR . '/core/filters/FilterChain.class.php');
require_once(LIMB_DIR . '/core/filters/CommandProcessingFilter.class.php');
require_once(LIMB_DIR . '/core/services/Service.class.php');
require_once(LIMB_DIR . '/core/commands/Command.interface.php');

Mock :: generate('FilterChain');
Mock :: generate('Service');
Mock :: generate('Command');

class CommandProcessingFilterTest extends LimbTestCase
{
  function CommandProcessingFilterTest()
  {
    parent :: LimbTestCase('command processing filter test');
  }

  function setUp()
  {
    Limb :: saveToolkit();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();
  }

  function testRunOk()
  {
    $toolkit =& Limb :: toolkit();

    $command = new MockCommand($this);
    $command->expectOnce('perform');

    $service =& new MockService($this);
    $service->expectOnce('getCurrentAction');
    $service->setReturnValue('getCurrentAction', $action = 'whatever');
    $service->expectOnce('getActionCommand', array($action));
    $service->setReturnReference('getActionCommand', $command);

    $toolkit->setCurrentService($service);

    $filter = new CommandProcessingFilter();

    $fc = new MockFilterChain($this);
    $fc->expectOnce('next');

    $filter->run($fc, $request, $response);

    $fc->tally();
    $service->tally();
    $command->tally();
  }

}

?>
