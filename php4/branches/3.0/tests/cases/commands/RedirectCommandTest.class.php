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
require_once(LIMB_DIR . '/core/commands/RedirectCommand.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/request/HttpResponse.class.php');

Mock :: generate('HttpResponse');
Mock :: generate('LimbBaseToolkit', 'MockLimbToolkit');

class RedirectCommandTest extends LimbTestCase
{
  function RedirectCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function testPerformOk()
  {
    $response =& new MockHttpResponse($this);
    $toolkit =& new MockLimbToolkit($this);

    $toolkit->setReturnReference('getResponse', $response);

    Limb :: registerToolkit($toolkit);

    $command = new RedirectCommand($path = '/somewhere');

    $response->expectOnce('redirect', array($path));

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $response->tally();
    Limb :: restoreToolkit();
  }
}

?>