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
require_once(LIMB_DIR . '/class/core/commands/DisplayViewCommand.class.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/class/core/request/Response.interface.php');
require_once(LIMB_DIR . '/class/template/Template.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('Response');

class TemplateStub extends Template
{
  function __construct()
  {
  }

  public function display()
  {
    echo 'test template';
  }
}

class DisplayViewCommandTest extends LimbTestCase
{
  var $toolkit;
  var $response;
  var $template;

  function setUp()
  {
    $this->toolkit = new MockLimbToolkit($this);
    $this->response = new MockResponse($this);
    $this->template = new TemplateStub();

    $this->toolkit->setReturnValue('getResponse', $this->response);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    Limb :: popToolkit();

    $this->toolkit->tally();
    $this->response->tally();
  }

  function testPerformOk()
  {
    $command = new DisplayViewCommand();

    $this->toolkit->expectOnce('getView');
    $this->toolkit->setReturnValue('getView', $this->template);

    $this->response->expectOnce('write', array('test template'));

    $this->assertEqual($command->perform(), Limb :: STATUS_OK);
  }

  function testPerformFailedNoView()
  {
    $command = new DisplayViewCommand();

    $this->toolkit->expectOnce('getView');
    $this->toolkit->setReturnValue('getView', null);

    try
    {
      $command->perform();
      $this->assertTrue(false);
    }
    catch(LimbException $e)
    {
    }
  }
}

?>