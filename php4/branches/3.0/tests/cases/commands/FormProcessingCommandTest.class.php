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
require_once(LIMB_DIR . '/core/commands/FormProcessingCommand.class.php');
require_once(WACT_ROOT . '/validation/validator.inc.php');
require_once(WACT_ROOT . '/template/template.inc.php');

class FormProcessingCommandTest extends LimbTestCase
{
  function FormProcessingCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $template = "<form id='test_form' runat='server'></form>";
    registerTestingTemplate('FormProcessingCommandTest.html', $template);

    $toolkit =& Limb :: saveToolkit();

    $template = new Template('FormProcessingCommandTest.html');
    $toolkit->setView($template);
  }

  function tearDown()
  {
    clearTestingTemplates();

    Limb :: restoreToolkit();
  }

  function testPerformFormDisplayed()
  {
    $validator = new Validator();
    $command = new FormProcessingCommand('test_form', LIMB_SINGLE_FORM, $validator);
    $this->assertEqual($command->perform(), LIMB_STATUS_FORM_DISPLAYED);
  }

  function testPerformFormValid()
  {
    $validator = new Validator();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('submitted', 1);
    $command = new FormProcessingCommand('test_form', LIMB_SINGLE_FORM, $validator);
    $this->assertEqual($command->perform(), LIMB_STATUS_OK);
  }
}

?>