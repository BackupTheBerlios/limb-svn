<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: save_new_object_access_command_test.class.php 818 2004-10-22 09:31:58Z seregalimb $
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/../../../commands/login/LogoutCommand.class.php');
require_once(LIMB_DIR . '/core/request/HttpResponse.class.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/core/permissions/User.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('HttpResponse');
Mock :: generate('User');

class LogoutCommandTest extends LimbTestCase
{
  var $command;
  var $response;
  var $toolkit;
  var $user;

  function setUp()
  {
    $this->response = new MockHttpResponse($this);
    $this->user = new MockUser($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnReference('getUser', $this->user);
    $this->toolkit->setReturnReference('getResponse', $this->response);

    Limb :: registerToolkit($this->toolkit);

    $this->command = new LogoutCommand($this);
  }

  function tearDown()
  {
    Limb :: popToolkit();

    $this->response->tally();
    $this->user->tally();
    $this->toolkit->tally();
  }

  function testPerform()
  {
    $this->user->expectOnce('logout');
    $this->response->expectOnce('redirect', array('/'));

    $this->assertEqual(LIMB_STATUS_OK, $this->command->perform());
  }
}

?>