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
require_once(dirname(__FILE__) . '/../../SimpleACLAuthorizer.class.php');
require_once(dirname(__FILE__) . '/../../SimpleACLIniBasedPolicyLoader.class.php');
require_once(LIMB_DIR . '/core/util/Ini.class.php');

Mock :: generate('SimpleACLAuthorizer');

class SimpleACLIniBasedPolicyLoaderTest extends LimbTestCase
{
  var $loader;

  function SimpleACLIniBasedPolicyLoaderTest()
  {
    parent :: LimbTestCase('Simple ACL ini based policy loader test');
  }

  function setUp()
  {
    $this->loader = new SimpleACLIniBasedPolicyLoader();
  }

  function tearDown()
  {
    clearTestingIni();
  }

  function testLoadPolicy()
  {
    registerTestingIni(
      'acl.ini',
      '
      policy[] = /root  admins  128
      policy[] = /root  visitors 1

      user[] = some user data
      '
    );

    $authorizer = new MockSimpleACLAuthorizer($this);

    $authorizer->expectArgumentsAt(0, 'attachPolicy', array('/root', 'admins', 128));
    $authorizer->expectArgumentsAt(1,'attachPolicy', array('/root', 'visitors', 1));

    $this->loader->load($authorizer);

    $authorizer->tally();

    clearTestingIni();
  }

  function testLoadPolicyFailedNoOptions()
  {
    registerTestingIni(
      'acl.ini',
      '
      user[] = some user data
      '
    );

    $authorizer = new MockSimpleACLAuthorizer($this);

    $authorizer->expectNever('attachPolicy');

    $this->loader->load($authorizer);

    $authorizer->tally();

    clearTestingIni();
  }

  function testLoadPolicyNoIni()
  {
    $authorizer = new MockSimpleACLAuthorizer($this);

    $authorizer->expectNever('attachPolicy');

    $this->loader->load($authorizer);

    $authorizer->tally();
  }
}

?>