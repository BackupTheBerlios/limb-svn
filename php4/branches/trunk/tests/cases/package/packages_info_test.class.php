<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
define('TEST_PACKAGE_INFO_DIR1', 'path1');
define('TEST_PACKAGE_INFO_DIR2', 'path2');

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
    unset($this->info);
  }
  
  function test_instance()
  {
    $this->assertReference($i1 =& packages_info :: instance(), $i2 =& packages_info :: instance());
  }
  
  function test_get_packages_from_ini()
  {
    register_testing_ini(
      'packages.ini',
      ' 
      [TEST_PACKAGE1]
       path = {TEST_PACKAGE_INFO_DIR1}/test1      
      [TEST_PACKAGE2]
       path = {TEST_PACKAGE_INFO_DIR2}/test2
      '
    );

    $this->assertEqual($this->info->get_packages(), 
      array(
        array('path' => TEST_PACKAGE_INFO_DIR1 . '/test1'),
        array('path' => TEST_PACKAGE_INFO_DIR2 . '/test2')
      )
    );
    
    $this->assertTrue(defined('TEST_PACKAGE1_DIR'));
    $this->assertTrue(defined('TEST_PACKAGE2_DIR'));
    $this->assertEqual(constant('TEST_PACKAGE1_DIR'), TEST_PACKAGE_INFO_DIR1 . '/test1');
    $this->assertEqual(constant('TEST_PACKAGE2_DIR'), TEST_PACKAGE_INFO_DIR2 . '/test2');
    
    clear_testing_ini();
  }   
}

?>