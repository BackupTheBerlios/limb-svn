<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
define('PACKAGES_DIR_FOR_PACKAGES_INFO_TEST', dirname(__FILE__) . '/packages/');

require_once(LIMB_DIR . '/class/core/packages_info.class.php');

class packages_info_test extends LimbTestCase
{
  var $info;
  
  function setUp()
  {
    $this->info =& new packages_info();
  }
  
  function tearDown()
  {
    $this->info->reset();
    unset($this->info);
  }
  
  function test_instance()
  {
    $this->assertTrue(packages_info :: instance() === packages_info :: instance());
  }
  
  function test_get_packages_from_ini()
  {
    register_testing_ini(
      'packages.ini',
      ' 
       packages[] = {PACKAGES_DIR_FOR_PACKAGES_INFO_TEST}test1      
       packages[] = {PACKAGES_DIR_FOR_PACKAGES_INFO_TEST}test2
      '
    );

    $this->assertEqual($this->info->get_packages(), 
      array(
        array('path' => PACKAGES_DIR_FOR_PACKAGES_INFO_TEST . 'test1', 'name' => 'PACKAGE1_FOR_PACKAGES_INFO_TEST'),
        array('path' => PACKAGES_DIR_FOR_PACKAGES_INFO_TEST . 'test2', 'name' => 'PACKAGE2_FOR_PACKAGES_INFO_TEST')
      )
    );
    
    $this->assertTrue(defined('PACKAGE1_FOR_PACKAGES_INFO_TEST_DIR'));
    $this->assertTrue(defined('PACKAGE2_FOR_PACKAGES_INFO_TEST_DIR'));
    $this->assertEqual(constant('PACKAGE1_FOR_PACKAGES_INFO_TEST_DIR'), PACKAGES_DIR_FOR_PACKAGES_INFO_TEST . 'test1');
    $this->assertEqual(constant('PACKAGE2_FOR_PACKAGES_INFO_TEST_DIR'), PACKAGES_DIR_FOR_PACKAGES_INFO_TEST . 'test2');
    
    clear_testing_ini();
  }   
}

?>