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
require_once(LIMB_DIR . '/class/permissions/User.class.php');
require_once(dirname(__FILE__) . '/../../../SimpleAuthenticator.class.php');

Mock :: generatePartial(
  'SimpleAuthenticator',
  'SpecialSimpleAuthenticator',
  array(
    '_getIdentityRecord',
    '_getDbGroups',
    '_getDefaultDbGroups',
  )
);

class SimpleAuthenticatorTest extends LimbTestCase
{
  var $auth;

  function setUp()
  {
    $inst =& User :: instance();
    $inst->logout();
    $this->auth = new SpecialSimpleAuthenticator($this);
  }

  function tearDown()
  {
    $inst =& User :: instance();
    $inst->logout();
    $this->auth->tally();
  }

  function testLoginRequired()
  {
    $this->auth->login(array('password' => 1));
    $this->assertTrue(catch('Exception', $e));
  }

  function testPasswordRequired()
  {
    $this->auth->login(array('login' => 1));
    $this->assertTrue(catch('Exception', $e));
  }

  function testLogout()
  {
    $user =& User :: instance();
    $user->set('login', 'some_user');

    $user->logout();
    $this->assertFalse($user->isLoggedIn());
    $this->assertFalse($user->get('login'));
  }

  function testLoginOk()
  {
    $this->auth->expectOnce('_getIdentityRecord');
    $this->auth->expectOnce('_getDbGroups');
    $this->auth->expectNever('_getDefaultDbGroups');
    $this->auth->setReturnValue('_getIdentityRecord', array('id' => 10, 'title' => 'user'));
    $this->auth->setReturnValue('_getDbGroups', array(0 => array('object_id' => 1, 'identifier' => 'visitors')));

    $this->auth->login(array('login' => 'some_user', 'password' => 'test', 'locale_id' => 'en'));

    $user =& User :: instance();
    $groups = $user->get('groups');
    $this->assertEqual(sizeof($groups), 1);
    $this->assertTrue(in_array('visitors', $groups));
    $this->assertEqual($user->get('title'), 'user');
    $this->assertEqual($user->get('locale_id'), 'en');
    $this->assertEqual($user->getId(), 10);
  }

  function testDefaultVisitorGroup()
  {
    $this->auth->expectOnce('_getIdentityRecord');
    $this->auth->expectOnce('_getDefaultDbGroups');
    $this->auth->expectNever('_getDbGroups');
    $this->auth->setReturnValue('_getIdentityRecord', false);
    $this->auth->setReturnValue('_getDefaultDbGroups', array(0 => array('object_id' => 1, 'identifier' => 'visitors')));

    $this->auth->login(array('login' => 'some_user', 'password' => 'test', 'locale_id' => 'en'));

    $inst =& User :: instance();
    $groups = $inst->get('groups');
    $this->assertEqual(sizeof($groups), 1);
    $this->assertTrue(in_array('visitors', $groups));
  }

  function testUserGetGroups()
  {
    $this->auth->expectOnce('_getIdentityRecord');
    $this->auth->expectOnce('_getDbGroups');
    $this->auth->expectNever('_getDefaultDbGroups');
    $this->auth->setReturnValue('_getIdentityRecord', array('someData'));
    $this->auth->setReturnValue('_getDbGroups', array(0 => array('object_id' => 1, 'identifier' => 'admins')));

    $this->auth->login(array('login' => 'some_user', 'password' => 'test', 'locale_id' => 'en'));

    $inst =& User :: instance();
    $groups = $inst->get('groups');
    $this->assertEqual(sizeof($groups), 1);
    $this->assertTrue(in_array('admins', $groups));
  }


  function testUserInGroupsMethod()
  {
    $user =& User :: instance();

    $groups = array(
      0 => 'visitors',
      1 => 'admins',
    );

    $user->set('groups', $groups);

    $this->assertTrue(SimpleAuthenticator :: isUserInGroups(array(0 => 'members', 'admins')));
    $this->assertFalse(SimpleAuthenticator :: isUserInGroups(array(0 => 'members', 'operators')));
    $this->assertFalse(SimpleAuthenticator :: isUserInGroups(array(0 => 'members')));

    $this->assertTrue(SimpleAuthenticator :: isUserInGroups(array(0 => 'visitors')));
  }
}
?>