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
require_once(LIMB_DIR . '/core/commands/DisplayViewCommand.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/request/Response.interface.php');

require_once(WACT_ROOT . '/template/template.inc.php');

Mock :: generate('LimbBaseToolkit', 'MockLimbToolkit');
Mock :: generate('Response');

class TemplateStub extends Template
{
  function TemplateStub()
  {
    //do nothing
  }

  function display()
  {
    echo 'test template';
  }
}

class DisplayViewCommandTest extends LimbTestCase
{
  var $toolkit;
  var $response;
  var $template;

  function DisplayViewCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->toolkit = new MockLimbToolkit($this);
    $this->response = new MockResponse($this);
    $this->template = new TemplateStub();

    $this->toolkit->setReturnReference('getResponse', $this->response);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    Limb :: restoreToolkit();

    $this->toolkit->tally();
    $this->response->tally();
  }

  function testPerformOk()
  {
    $command = new DisplayViewCommand();

    $this->toolkit->expectOnce('getView');
    $this->toolkit->setReturnReference('getView', $this->template);

    $this->response->expectOnce('write', array('test template'));

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);
  }

  function testPerformFailedNoView()
  {
    $command = new DisplayViewCommand();

    $this->toolkit->expectOnce('getView');
    $this->toolkit->setReturnValue('getView', null);

    die_on_error(false);
    $command->perform();
    $this->assertTrue(catch_error('LimbException', $e));
    die_on_error();
  }
}

?>