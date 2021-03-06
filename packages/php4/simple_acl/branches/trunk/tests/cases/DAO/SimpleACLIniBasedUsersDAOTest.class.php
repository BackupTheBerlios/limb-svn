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
require_once(dirname(__FILE__) . '/../../../dao/SimpleACLIniBasedUsersDAO.class.php');
require_once(LIMB_DIR . '/core/util/Ini.class.php');

class SimpleACLIniBasedUsersDAOTest extends LimbTestCase
{
  var $finder;

  function SimpleACLIniBasedUsersDAOTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    registerTestingIni(
      'acl.ini',
      '
      users[] = vasa:0ef9e88d20d59114a89dad73c2bc0625:Vasa Pupkin:test@example.com:visitors
      users[] = admin:0ef9e88d20d59114a89dad73c2bc0625:Super Admin:admin@test.com:admins
        '
    );

    $this->dao = new SimpleACLIniBasedUsersDAO();
  }

  function tearDown()
  {
    clearTestingIni();
  }

  function testFindByLogin()
  {
    $user_data = $this->dao->findByLogin('vasa');

    $this->assertEqual($user_data->get('login'), 'vasa');
    $this->assertEqual($user_data->get('password'), '0ef9e88d20d59114a89dad73c2bc0625');
    $this->assertEqual($user_data->get('name'), 'Vasa Pupkin');
    $this->assertEqual($user_data->get('email'), 'test@example.com');
    $this->assertEqual($user_data->get('groups'), array('visitors'));
  }

  function testFindByLoginFailedNoSuchUser()
  {
    $this->assertFalse($this->dao->findByLogin('serega'));
  }
}

?>