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
require_once(LIMB_DIR . '/class/commands/UseViewCommand.class.php');
require_once(LIMB_DIR . '/class/LimbToolkit.interface.php');

Mock :: generate('LimbToolkit');

class UseViewCommandTest extends LimbTestCase
{
  var $toolkit;

  function UseViewCommandTest()
  {
    parent :: LimbTestCase('use view cmd test');
  }

  function setUp()
  {
    $this->toolkit = new MockLimbToolkit($this);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    Limb :: popToolkit();

    $this->toolkit->tally();
  }

  function testPerformOk()
  {
    $command = new UseViewCommand('/test.html');

    $handle = array(LIMB_DIR . '/class/template/template', '/test.html');

    $this->toolkit->expectOnce('setView', array($handle));

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);
  }
}

?>