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
define('PACKAGES_DIR_FOR_PACKAGES_INFO_TEST', dirname(__FILE__) . '/packages/');

require_once(LIMB_DIR . '/class/core/PackagesInfo.class.php');

class PackagesInfoTest extends LimbTestCase
{
  var $info;

  function setUp()
  {
    $this->info = new PackagesInfo();
  }

  function tearDown()
  {
    $this->info->reset();
    unset($this->info);
  }

  function testInstance()
  {
    $this->assertTrue(PackagesInfo :: instance() === PackagesInfo :: instance());
  }

  function testGetPackagesFromIni()
  {
    registerTestingIni(
      'packages.ini',
      '
       packages[] = {PACKAGES_DIR_FOR_PACKAGES_INFO_TEST}test1
       packages[] = {PACKAGES_DIR_FOR_PACKAGES_INFO_TEST}test2
      '
    );

    $this->assertEqual($this->info->getPackages(),
      array(
        array('path' => PACKAGES_DIR_FOR_PACKAGES_INFO_TEST . 'test1',
              'name' => 'PACKAGE1_FOR_PACKAGES_INFO_TEST'),
        array('path' => PACKAGES_DIR_FOR_PACKAGES_INFO_TEST . 'test2',
              'name' => 'PACKAGE2_FOR_PACKAGES_INFO_TEST')
      )
    );

    $this->assertTrue(defined('PACKAGE1_FOR_PACKAGES_INFO_TEST_DIR'));
    $this->assertTrue(defined('PACKAGE2_FOR_PACKAGES_INFO_TEST_DIR'));
    $this->assertEqual(constant('PACKAGE1_FOR_PACKAGES_INFO_TEST_DIR'), PACKAGES_DIR_FOR_PACKAGES_INFO_TEST . 'test1');
    $this->assertEqual(constant('PACKAGE2_FOR_PACKAGES_INFO_TEST_DIR'), PACKAGES_DIR_FOR_PACKAGES_INFO_TEST . 'test2');

    clearTestingIni();
  }
}

?>