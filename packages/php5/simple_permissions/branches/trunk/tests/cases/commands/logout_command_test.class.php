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
require_once(dirname(__FILE__) . '/../../../commands/login/logout_command.class.php');
require_once(LIMB_DIR . '/class/core/request/http_response.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('http_response');
Mock :: generate('user');

class logout_command_test extends LimbTestCase
{
  var $command;
  var $response;
  var $toolkit;
  var $user;

  function setUp()
  {
    $this->response = new Mockhttp_response($this);
    $this->user = new Mockuser($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getUser', $this->user);
    $this->toolkit->setReturnValue('getResponse', $this->response);

    Limb :: registerToolkit($this->toolkit);

    $this->command = new logout_command($this);
  }

  function tearDown()
  {
    Limb :: popToolkit();

    $this->response->tally();
    $this->user->tally();
    $this->toolkit->tally();
  }

  function test_perform()
  {
    $this->user->expectOnce('logout');
    $this->response->expectOnce('redirect', array('/'));

    $this->assertEqual(Limb :: STATUS_OK, $this->command->perform());
  }
}

?>