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
require_once(dirname(__FILE__) . '/../../../commands/login/LoginCommand.class.php');
require_once(LIMB_DIR . '/class/core/request/HttpResponse.class.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/class/core/permissions/User.class.php');
require_once(LIMB_DIR . '/class/core/permissions/Authenticator.interface.php');
require_once(LIMB_DIR . '/class/core/Dataspace.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('HttpResponse');
Mock :: generate('User');
Mock :: generate('Authenticator');

Mock :: generatePartial('LoginCommand',
                         'LoginCommandTestVersion',
                         array('_getHttpReferer'));

class LoginCommandTest extends LimbTestCase
{
  var $command;
  var $response;
  var $toolkit;
  var $user;
  var $dataspace;
  var $authenticator;

  function setUp()
  {
    $this->response = new MockHttpResponse($this);
    $this->user = new MockUser($this);
    $this->dataspace = new Dataspace();
    $this->authenticator = new MockAuthenticator($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getUser', $this->user);
    $this->toolkit->setReturnValue('getResponse', $this->response);
    $this->toolkit->setReturnValue('getDataspace', $this->dataspace);
    $this->toolkit->setReturnValue('getAuthenticator', $this->authenticator);

    Limb :: registerToolkit($this->toolkit);

    $this->command = new LoginCommandTestVersion($this);
  }

  function tearDown()
  {
    Limb :: popToolkit();

    $this->response->tally();
    $this->user->tally();
    $this->toolkit->tally();
    $this->authenticator->tally();
  }

  function testPerformFailed()
  {
    $login_data = array('login' => 'test_login',
                        'password' => 'test_password',
                        'locale_id' => 'en');

    $this->dataspace->import($login_data);

    $this->authenticator->expectOnce('login', array($login_data));
    $this->user->expectOnce('isLoggedIn');
    $this->user->setReturnValue('isLoggedIn', false);

    $this->assertEqual(Limb :: STATUS_ERROR, $this->command->perform());
  }

  function testPerformOkRedirectToRoot()
  {
    $login_data = array('login' => 'test_login',
                        'password' => 'test_password',
                        'locale_id' => 'en');

    $this->dataspace->import($login_data);

    $this->authenticator->expectOnce('login', array($login_data));
    $this->user->expectOnce('isLoggedIn');
    $this->user->setReturnValue('isLoggedIn', true);

    $this->response->expectOnce('redirect', array('/'));
    $this->command->setReturnValue('_getHttpReferer', '');

    $this->assertEqual(Limb :: getSTATUS_OK(), $this->command->perform());
  }

  function testPerformOkRedirectToRootButLogin()
  {
    $login_data = array('login' => 'test_login',
                        'password' => 'test_password',
                        'locale_id' => 'en');

    $this->dataspace->import($login_data);

    $this->authenticator->expectOnce('login', array($login_data));
    $this->user->expectOnce('isLoggedIn');
    $this->user->setReturnValue('isLoggedIn', true);

    $this->response->expectOnce('redirect', array('/'));
    $this->command->setReturnValue('_getHttpReferer', '/root/login');

    $this->assertEqual(Limb :: getSTATUS_OK(), $this->command->perform());
  }


  function testPerformOkRedirectToReferer()
  {
    $login_data = array('login' => 'test_login',
                        'password' => 'test_password',
                        'locale_id' => 'en');

    $this->dataspace->import($login_data);

    $this->authenticator->expectOnce('login', array($login_data));
    $this->user->expectOnce('isLoggedIn');
    $this->user->setReturnValue('isLoggedIn', true);

    $this->command->setReturnValue('_getHttpReferer', $refer = 'someReferer');

    $this->response->expectOnce('redirect', array($refer));

    $this->assertEqual(Limb :: getSTATUS_OK(), $this->command->perform());
  }

  function testPerformOkRedirectToRedirectParam()
  {
    $login_data = array('login' => 'test_login',
                        'password' => 'test_password',
                        'locale_id' => 'en');

    $this->dataspace->import($login_data);

    $this->dataspace->set('redirect', $some_redirect = 'some_redirect');

    $this->authenticator->expectOnce('login', array($login_data));
    $this->user->expectOnce('isLoggedIn');
    $this->user->setReturnValue('isLoggedIn', true);

    $this->command->expectNever('_getHttpReferer');

    $this->response->expectOnce('redirect', array($some_redirect));

    $this->assertEqual(Limb :: getSTATUS_OK(), $this->command->perform());
  }
}

?>