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
require_once(dirname(__FILE__) . '/../../../commands/login/login_command.class.php');
require_once(LIMB_DIR . '/class/core/request/http_response.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');
require_once(LIMB_DIR . '/class/core/permissions/authenticator.interface.php');
require_once(LIMB_DIR . '/class/core/dataspace.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('http_response');
Mock :: generate('user');
Mock :: generate('authenticator');

Mock :: generatePartial('login_command',
                         'login_command_test_version',
                         array('_get_http_referer'));

class login_command_test extends LimbTestCase
{
  var $command;
  var $response;
  var $toolkit;
  var $user;
  var $dataspace;
  var $authenticator;

  function setUp()
  {
    $this->response = new Mockhttp_response($this);
    $this->user = new Mockuser($this);
    $this->dataspace = new dataspace();
    $this->authenticator = new Mockauthenticator($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getUser', $this->user);
    $this->toolkit->setReturnValue('getResponse', $this->response);
    $this->toolkit->setReturnValue('getDataspace', $this->dataspace);
    $this->toolkit->setReturnValue('getAuthenticator', $this->authenticator);

    Limb :: registerToolkit($this->toolkit);

    $this->command = new login_command_test_version($this);
  }

  function tearDown()
  {
    Limb :: popToolkit();

    $this->response->tally();
    $this->user->tally();
    $this->toolkit->tally();
    $this->authenticator->tally();
  }

  function test_perform_failed()
  {
    $login_data = array('login' => 'test_login',
                        'password' => 'test_password',
                        'locale_id' => 'en');

    $this->dataspace->import($login_data);

    $this->authenticator->expectOnce('login', array($login_data));
    $this->user->expectOnce('is_logged_in');
    $this->user->setReturnValue('is_logged_in', false);

    $this->assertEqual(Limb :: STATUS_ERROR, $this->command->perform());
  }

  function test_perform_ok_redirect_to_root()
  {
    $login_data = array('login' => 'test_login',
                        'password' => 'test_password',
                        'locale_id' => 'en');

    $this->dataspace->import($login_data);

    $this->authenticator->expectOnce('login', array($login_data));
    $this->user->expectOnce('is_logged_in');
    $this->user->setReturnValue('is_logged_in', true);

    $this->response->expectOnce('redirect', array('/'));
    $this->command->setReturnValue('_get_http_referer', '');

    $this->assertEqual(Limb :: STATUS_OK, $this->command->perform());
  }

  function test_perform_ok_redirect_to_root_but_login()
  {
    $login_data = array('login' => 'test_login',
                        'password' => 'test_password',
                        'locale_id' => 'en');

    $this->dataspace->import($login_data);

    $this->authenticator->expectOnce('login', array($login_data));
    $this->user->expectOnce('is_logged_in');
    $this->user->setReturnValue('is_logged_in', true);

    $this->response->expectOnce('redirect', array('/'));
    $this->command->setReturnValue('_get_http_referer', '/root/login');

    $this->assertEqual(Limb :: STATUS_OK, $this->command->perform());
  }


  function test_perform_ok_redirect_to_referer()
  {
    $login_data = array('login' => 'test_login',
                        'password' => 'test_password',
                        'locale_id' => 'en');

    $this->dataspace->import($login_data);

    $this->authenticator->expectOnce('login', array($login_data));
    $this->user->expectOnce('is_logged_in');
    $this->user->setReturnValue('is_logged_in', true);

    $this->command->setReturnValue('_get_http_referer', $refer = 'some_referer');

    $this->response->expectOnce('redirect', array($refer));

    $this->assertEqual(Limb :: STATUS_OK, $this->command->perform());
  }

  function test_perform_ok_redirect_to_redirect_param()
  {
    $login_data = array('login' => 'test_login',
                        'password' => 'test_password',
                        'locale_id' => 'en');

    $this->dataspace->import($login_data);

    $this->dataspace->set('redirect', $some_redirect = 'some_redirect');

    $this->authenticator->expectOnce('login', array($login_data));
    $this->user->expectOnce('is_logged_in');
    $this->user->setReturnValue('is_logged_in', true);

    $this->command->expectNever('_get_http_referer');

    $this->response->expectOnce('redirect', array($some_redirect));

    $this->assertEqual(Limb :: STATUS_OK, $this->command->perform());
  }
}

?>