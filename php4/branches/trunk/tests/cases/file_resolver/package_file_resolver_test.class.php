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
  
  
  function test_resolve_file_name()
  {
    register_testing_ini(
      'packages.ini',
      ' 
      [package_1]
       path = {TEST_PACKAGE_LOCAL_DIR}package1/
      [package_2]
       path = {TEST_PACKAGE_COMMON_DIR}package2/1.0/
      [package_3]
       path = {TEST_PACKAGE_COMMON_DIR}package3/1.1/
      '
    );
    
    $this->assertEqual($this->file_resolver->resolve('package2_action'), TEST_PACKAGE_COMMON_DIR . 'package2/1.0/package2_action');

    clear_testing_ini();
  }

}

?>