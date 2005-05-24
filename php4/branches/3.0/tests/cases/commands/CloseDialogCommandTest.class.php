<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: UseViewCommandTest.class.php 1209 2005-04-08 14:29:41Z pachanga $
*
***********************************************************************************/
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(LIMB_DIR . '/core/commands/CloseDialogCommand.class.php');

class CloseDialogCommandTest extends LimbTestCase
{
  function CloseDialogCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    Limb :: saveToolkit();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();
  }

  function testPerformOk()
  {
    RegisterTestingTemplate('/close_popup.html', "<list:LIST id='params'>" .
                                                 "<list:ITEM>{\$name},{\$value}</list:ITEM>".
                                                 "</list:LIST>");

    $command = new CloseDialogCommand();

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $toolkit =& Limb :: toolkit();
    $response =& $toolkit->getResponse();

    $this->assertEqual($response->getResponseString(), 'from_dialog,1');
  }
}

?>