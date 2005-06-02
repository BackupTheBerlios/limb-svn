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

class CommandStub
{
  var $performed = false;

  function perform()
  {
    $this->performed = true;
  }
}

class CommandProcessingFilterTest extends LimbTestCase
{
  var $toolkit;

  function CommandProcessingFilterTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->toolkit =& Limb :: saveToolkit();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();
  }

  function testRunOk()
  {
    $command = new CommandStub();

    $service =& new MockService($this);
    $service->expectOnce('getCurrentAction');
    $service->setReturnValue('getCurrentAction', $action = 'whatever');
    $service->expectOnce('getActionCommand', array($action));
    $service->setReturnReference('getActionCommand', $command);

    $this->toolkit->setService($service);

    $filter = new CommandProcessingFilter();

    $fc = new MockFilterChain($this);
    $fc->expectOnce('next');

    $filter->run($fc, $request, $response);

    $this->assertTrue($command->performed);

    $fc->tally();
    $service->tally();
  }
}

?>
