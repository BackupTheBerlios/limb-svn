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
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');
require_once(dirname(__FILE__) . '/../../../simple_authenticator.class.php');

Mock :: generatePartial(
  'simple_authenticator',
  'special_simple_authenticator',
  array(
    '_get_identity_record',
    '_get_db_groups',
    '_get_default_db_groups',
  )
);


class simple_authenticator_test extends LimbTestCase
{
  var $auth;

  function setUp()
  {
    user :: instance()->logout();
    $this->auth = new special_simple_authenticator($this);
  }

  function tearDown()
  {
    user :: instance()->logout();
    $this->auth->tally();
  }

  function test_login_required()
  {
    try
    {
      $this->auth->login(array('password' => 1));
      $this->assertTrue(false);
    }
    catch(LimbException $e)
    {
    }
  }

  function test_password_required()
  {
    try
    {
      $this->auth->login(array('login' => 1));
      $this->assertTrue(false);
    }
    catch(LimbException $e)
    {
    }
  }

  function test_logout()
  {
    $user = user :: instance();
    $user->set('login', 'some_user');

    $user->logout();
    $this->assertFalse($user->is_logged_in());
    $this->assertFalse($user->get('login'));
  }

  function test_login_ok()
  {
    $this->auth->expectOnce('_get_identity_record');
    $this->auth->expectOnce('_get_db_groups');
    $this->auth->expectNever('_get_default_db_groups');
    $this->auth->setReturnValue('_get_identity_record', array('id' => 10, 'title' => 'User'));
    $this->auth->setReturnValue('_get_db_groups', array(0 => array('object_id' => 1, 'identifier' => 'visitors')));

    $this->auth->login(array('login' => 'some_user', 'password' => 'test', 'locale_id' => 'en'));

    $user = user :: instance();
    $groups = $user->get('groups');
    $this->assertEqual(sizeof($groups), 1);
    $this->assertTrue(in_array('visitors', $groups));
    $this->assertEqual($user->get('title'), 'User');
    $this->assertEqual($user->get('locale_id'), 'en');
    $this->assertEqual($user->get_id(), 10);
  }

  function test_default_visitor_group()
  {
    $this->auth->expectOnce('_get_identity_record');
    $this->auth->expectOnce('_get_default_db_groups');
    $this->auth->expectNever('_get_db_groups');
    $this->auth->setReturnValue('_get_identity_record', false);
    $this->auth->setReturnValue('_get_default_db_groups', array(0 => array('object_id' => 1, 'identifier' => 'visitors')));

    $this->auth->login(array('login' => 'some_user', 'password' => 'test', 'locale_id' => 'en'));

    $groups = user :: instance()->get('groups');
    $this->assertEqual(sizeof($groups), 1);
    $this->assertTrue(in_array('visitors', $groups));
  }

  function test_user_get_groups()
  {
    $this->auth->expectOnce('_get_identity_record');
    $this->auth->expectOnce('_get_db_groups');
    $this->auth->expectNever('_get_default_db_groups');
    $this->auth->setReturnValue('_get_identity_record', array('some_data'));
    $this->auth->setReturnValue('_get_db_groups', array(0 => array('object_id' => 1, 'identifier' => 'admins')));

    $this->auth->login(array('login' => 'some_user', 'password' => 'test', 'locale_id' => 'en'));

    $groups = user :: instance()->get('groups');
    $this->assertEqual(sizeof($groups), 1);
    $this->assertTrue(in_array('admins', $groups));
  }


  function test_user_in_groups_method()
  {
    $user = user :: instance();

    $groups = array(
      0 => 'visitors',
      1 => 'admins',
    );

    $user->set('groups', $groups);

    $this->assertTrue(simple_authenticator :: is_user_in_groups(array(0 => 'members', 'admins')));
    $this->assertFalse(simple_authenticator :: is_user_in_groups(array(0 => 'members', 'operators')));
    $this->assertFalse(simple_authenticator :: is_user_in_groups(array(0 => 'members')));

    $this->assertTrue(simple_authenticator :: is_user_in_groups(array(0 => 'visitors')));
  }
}
?>