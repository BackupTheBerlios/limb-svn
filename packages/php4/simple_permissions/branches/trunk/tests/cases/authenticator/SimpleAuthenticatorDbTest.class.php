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
require_once(LIMB_DIR . '/core/permissions/User.class.php');
require_once(dirname(__FILE__) . '/../../../SimpleAuthenticator.class.php');

class SimpleAuthenticatorDbTest extends LimbTestCase
{
  var $auth;

  function setUp()
  {
    $this->auth = new SimpleAuthenticator();

    loadTestingDbDump(dirname(__FILE__) . '/../../sql/simple_authenticator.sql');
  }

  function tearDown()
  {
    clearTestingDbTables();
    $inst =& User :: instance();
    $inst->logout();
  }

  function testLoginOk()
  {
    $this->auth->login(array('login' => 'vasa', 'password' => '1', 'locale_id' => 'en'));

    $user =& User :: instance();

    $this->assertTrue($user->isLoggedIn());
    $this->assertEqual($user->getId(), 1);
    $this->assertEqual($user->getLogin(), 'vasa');
    $this->assertEqual($user->get('node_id'), 2);
    $this->assertEqual($user->get('groups'), array(3 => 'visitors', 4 => 'admins' ));
    $this->assertEqual($user->get('locale_id'), 'en');
  }

  function testLogout()
  {
    $this->auth->logout();

    $user =& User :: instance();

    $this->assertEqual($user->get('groups'), array(3 => 'visitors'));
  }
}
?>