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
require_once(dirname(__FILE__) . '/../../SimpleACLLoginCommand.class.php');

class SimpleACLLoginCommandTest extends LimbTestCase
{
  var $cmd;

  function SimpleACLLoginCommandTest()
  {
    parent :: LimbTestCase('Simple ACL login command test');
  }

  function setUp()
  {
    $this->cmd = new SimpleACLLoginCommand();
    Limb :: saveToolkit();
  }

  function tearDown()
  {
    clearTestingIni();
    Limb :: restoreToolkit();
  }

  function testLoginFailed()
  {
    registerTestingIni('acl.ini', '');

    $this->assertEqual($this->cmd->perform(),
                       LIMB_STATUS_ERROR);
  }

  function testLoginSuccess()
  {
    registerTestingIni('acl.ini',
                       'users[] = test:' . md5($password = 'test') . ':bill:test@dot.com:test');

    $toolkit =& Limb :: toolkit();
    $ds =& $toolkit->getDataspace();
    $ds->set('password', $password);
    $ds->set('login', 'test');

    $this->assertEqual($this->cmd->perform(),
                       LIMB_STATUS_OK);
  }

}

?>