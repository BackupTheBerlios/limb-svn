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
define('TEST_PACKAGE_LOCAL_DIR', dirname(__FILE__) . '/packages/local/');
define('TEST_PACKAGE_COMMON_DIR', dirname(__FILE__) . '/packages/common/');

require_once(LIMB_DIR . '/class/core/file_resolvers/package_file_resolver.class.php');

Mock::generatePartial(
  'package_file_resolver', 
  'package_file_resolver_test_version', 
  array('_find_file_in_packages')
);

class package_file_resolver_test extends LimbTestCase
{
  var $file_resolver;
  
  function setUp()
  {
    $this->file_resolver =& new package_file_resolver();
  }
  
  function tearDown()
  {
    unset($this->file_resolver);
  }
  
  function test_get_packages_from_ini()
  {
    register_testing_ini(
      'packages.ini',
      ' 
      [package-file]
       path = {TEST_PACKAGE_LOCAL_DIR}file/2.2.2/
      [package-specific_news]
       path = {TEST_PACKAGE_COMMON_DIR}news/2.2/
      '
    );

    $this->assertEqual($this->file_resolver->get_packages(), 
      array(
        array('path' => TEST_PACKAGE_LOCAL_DIR . 'file/2.2.2/'),
        array('path' => TEST_PACKAGE_COMMON_DIR . 'news/2.2/')
      )
    );
    
    clear_testing_ini();
  } 
  
  function test_resolve_file_name()
  {
    register_testing_ini(
      'packages.ini',
      ' 
      [package-1]
       path = {TEST_PACKAGE_LOCAL_DIR}package1/
      [package-2]
       path = {TEST_PACKAGE_COMMON_DIR}package2/1.0/
      [package-3]
       path = {TEST_PACKAGE_COMMON_DIR}package3/1.1/
      '
    );
    
    $this->assertEqual($this->file_resolver->resolve('package2_action'), TEST_PACKAGE_COMMON_DIR . 'package2/1.0/package2_action');

    clear_testing_ini();
  }
  
//  function test_resolve_write_to_cache()
//  {
//    register_testing_ini(
//      'packages.ini',
//      ' 
//      [package-2]
//       path = {TEST_PACKAGE_COMMON_DIR}package2/1.0/
//      '
//    );
//    
//    $this->file_resolver->resolve('package2_action');
//    
//    $file_name = VAR_DIR . 'cache/' . get_class($this->file_resolver). '.php';
//    $this->assertTrue(file_exists($file_name));
//    
//    include($file_name);
//    
//    $this->assertEqual($resolved_file_paths, array('package2_action' => TEST_PACKAGE_COMMON_DIR . 'package2/1.0/package2_action'));
//    unlink($file_name);
//    
//    clear_testing_ini();
//  }
//  
//  function test_resolve_read_from_cache()
//  {
//    register_testing_ini(
//      'packages.ini',
//      ' 
//      [package-2]
//       path = {TEST_PACKAGE_COMMON_DIR}package2/1.0/
//      '
//    );
//    
//    $this->file_resolver->resolve('package2_action');
//    
//    $other_file_resolver =& new package_file_resolver_test_version($this);
//    
//    $other_file_resolver->expectNever('_find_file_in_packages');
//    $other_file_resolver->resolve('package2_action');
//    $other_file_resolver->tally();
//
//    clear_testing_ini();
//  }
}

?>