<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbRepeatTagTest.class.php 1017 2005-01-13 12:10:15Z pachanga $
*
***********************************************************************************/
require_once(WACT_ROOT . '/template/template.inc.php');

class LimbUserTagsTestCase extends LimbTestCase
{
  function LimbUserTagsTestCase()
  {
    parent :: LimbTestCase('limb user related tags and properties case');
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testUserTag()
  {
    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();
    $user->set('login', $login = 'test_login');
    $user->set('name', $name = 'test_name');

    $template = '<limb:USER>{$login}-{$name}</limb:USER>';

    RegisterTestingTemplate('/limb/user.html', $template);

    $page =& new Template('/limb/user.html');

    $this->assertEqual($page->capture(), 'test_login-test_name');
  }

  function testIsNotLoggedIn()
  {
    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();
    $user->logout();

    $template = '<limb:USER><core:OPTIONAL for="UserIsLoggedIn">logged!</core:OPTIONAL>' .
                '<core:DEFAULT for="UserIsLoggedIn">not logged!</core:DEFAULT></limb:USER>';

    RegisterTestingTemplate('/limb/user_is_not_logged_in.html', $template);

    $page =& new Template('/limb/user_is_not_logged_in.html');

    $this->assertEqual($page->capture(), 'not logged!');
  }

  function testIsLoggedIn()
  {
    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();
    $user->login();

    $template = '<limb:USER><core:OPTIONAL for="UserIsLoggedIn">logged!</core:OPTIONAL>' .
                '<core:DEFAULT for="UserIsLoggedIn">not logged!</core:DEFAULT></limb:USER>';

    RegisterTestingTemplate('/limb/user_is_logged_in.html', $template);

    $page =& new Template('/limb/user_is_logged_in.html');

    $this->assertEqual($page->capture(), 'logged!');
  }
}
?>
