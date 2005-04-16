<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: PackagesInfoTest.class.php 1028 2005-01-18 11:06:55Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/permissions/User.class.php');

class UserTest extends LimbTestCase
{
  function UserTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function testLoginLogout()
  {
    $user = new User();
    $user->set('name', 'whatever');

    $user->login();

    $this->assertTrue($user->isLoggedIn());

    $user->logout();

    $this->assertFalse($user->isLoggedIn());

    $this->assertFalse($user->get('name'));
  }
}

?>