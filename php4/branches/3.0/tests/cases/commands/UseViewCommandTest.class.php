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
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(LIMB_DIR . '/core/commands/UseViewCommand.class.php');

class UseViewCommandTest extends LimbTestCase
{
  function UseViewCommandTest()
  {
    parent :: LimbTestCase('use view cmd test');
  }

  function testPerformOk()
  {
    RegisterTestingTemplate('/test.html', 'hello');

    $command = new UseViewCommand('/test.html');

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $toolkit =& Limb :: toolkit();
    $view = $toolkit->getView();

    $this->assertIsA($view, 'Template');
  }
}

?>