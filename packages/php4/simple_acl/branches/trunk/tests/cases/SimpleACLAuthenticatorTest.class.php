<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: ImageObjectsDAOTest.class.php 1093 2005-02-07 15:17:20Z pachanga $
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/../../SimpleACLAuthenticator.class.php');
require_once(LIMB_DIR . '/core/Service.class.php');
require_once(dirname(__FILE__) . '/../../DAO/SimpleACLAbstractUsersDAO.class.php');

Mock :: generate('SimpleACLAbstractUsersDAO', 'MockAbstractUsersDAO');

class SimpleACLAuthenticatorTest extends LimbTestCase
{
  var $authenticator;

  function SimpleACLAuthenticatorTest()
  {
    parent :: LimbTestCase('Simple ACL Authenticator test');
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();
    $user->logout();

    $this->authenticator = new SimpleACLAuthenticator();
  }

  function tearDown()
  {
    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();
    $user->logout();
  }

  function testLoginSuccess()
  {
    $users_dao = new MockAbstractUsersDAO($this);
    $this->authenticator->setUsersDAO($users_dao);

    $users_dao->expectOnce('findByLogin', array($login = 'test'));
    $password = 'test_password';
    $groups = 'some_groups';

    $user_data = new DataSpace();
    $user_data->import(array('password' => md5($password),
                              'groups' => $groups));

    $users_dao->setReturnReference('findByLogin', $user_data);

    $this->authenticator->login($login, $password);

    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();

    $this->assertTrue($user->isLoggedIn());
    $this->assertEqual($user->getGroups(), $groups);
    $this->assertEqual($user->getLogin(), $login);

    $users_dao->tally();
  }

  function testLoginFailedPasswordNotMatched()
  {
    $users_dao = new MockAbstractUsersDAO($this);
    $this->authenticator->setUsersDAO($users_dao);

    $users_dao->expectOnce('findByLogin', array($login = 'test'));

    $user_data = new DataSpace();
    $user_data->import(array('password' => md5('wrong_password')));

    $users_dao->setReturnReference('findByLogin', $user_data);

    $this->authenticator->login($login, $password = 'test_password');

    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();

    $this->assertFalse($user->isLoggedIn());

    $users_dao->tally();
  }
}

?>